
import java.util.Date
import net.liftweb.json.JsonAST.{JDouble, JField, JObject}
import scalaj.http.{Http, HttpOptions}
import slick.session.{Database, Session}
import slick.jdbc.{StaticQuery => Q}

object UpdateBalancesAndTickerData {

  def main(args: Array[String]) {
    withDatabaseConn { implicit s: Session =>
      updateAddressBalances
      updateTickerData
    }
  }

  def updateTickerData(implicit s: Session) {
    val resp = getRequest("http://blockchain.info/ticker")
    val tickers = parseTickerJson(resp)
    info("Got following ticker data:")
    tickers.foreach { t =>
      info(s"${t.currency} is trading at ${t.last} to the 'coin")
      // XXX: Why doesn't withTransaction work?!! Our data just disappears into the
      // XXX: ether when the queries are wrapped with it.
//      s.withTransaction { tran: Session =>
        Q.update[String]("DELETE FROM ticker_data WHERE currency = ?").execute(t.currency)
        Q.update[(String, Double)]("INSERT INTO ticker_data (currency, last_price) VALUES (?, ?)").
          execute(t.currency, t.last)
//      }
    }
  }

  private def parseTickerJson(js: String): List[TickerData] = {
    val parsed = net.liftweb.json.parse(js)
    parsed match {
      case JObject(list) =>
        list.map { f: JField =>
          val code = f.name
          val last = f.value match {
            case JObject(fields) =>
              fields.filter(_.name == "last") match {
                case List(JField(_, v: JDouble)) => v.values
                case v => throw new Exception("Expected 'last' value to be of type Double for currency " + code)
              }
            case _ => throw new Exception("Could not find 'last' value for currency " + code)
          }
          TickerData(code, last)
        }
      case _ => throw new Exception("ERROR: JSON was not in expected format")
    }
  }

  def updateAddressBalances(implicit s: Session) {
    val addresses = Q.queryNA[String]("SELECT DISTINCT address FROM widgets").list()
    addresses.foreach { a =>
      try {
        info(s"Requesting balance for address $a...")
        val resp = getRequest(s"http://blockchain.info/q/addressbalance/$a")
        try {
          val balance: Int = resp.toInt
          info(s"  Balance for address $a is $balance.")
          Q.update[String]("DELETE FROM bitcoin_addresses WHERE address = ?").execute(a)
          Q.update[(String, Int, String)](
            "INSERT INTO bitcoin_addresses (address, satoshis, updated_at) VALUES (?, ?, ?)").
            execute(a, balance, now)
        } catch {
          case e: java.lang.NumberFormatException =>
            logErr("Got non-integer response: " + resp)
        }
      } catch {
        case e: scalaj.http.HttpException =>
          logErr("Got HTTP " + e.code + " response: " + e.body)
      }
    }
  }

  def now: String = {
    val df = new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
    df.format(new Date)
  }

  val baseHeaders = List(
    ("User-Agent", "Mozilla/4.0 (compatible; Test client)"))

  def getRequest(theURL: String): String = {
    Http(theURL).headers(baseHeaders).
      option(HttpOptions.connTimeout(3000)).
      option(HttpOptions.readTimeout(5000)).asString
  }

  protected def withDatabaseConn/*(propFile: String)*/(doWithConn: (Session) => Unit) {
    val propFile = System.getenv("CONF")
    if (propFile == null || propFile.trim == "")
      throw new Exception("CONF environment variable not set!")
    implicit val props = readPropertiesFile(propFile)
    val adapter = getProp("resources.db.adapter").toLowerCase
    if (adapter != "mysqli")
      throw new Exception(s"Expected DB adapter to be 'mysqli' (but got '$adapter')")
    def get(p: String) = getProp(s"resources.db.params.$p")
    val dbHost = get("host")
    val dbName = get("dbname")
    val dbUser = get("username")
    val dbPass = get("password")
    val dbDriverName = (new com.mysql.jdbc.Driver).getClass.getName
    Database.forURL("jdbc:mysql://" + dbHost + "/" + dbName, driver = dbDriverName,
      user = dbUser, password = dbPass).withSession { s: Session =>
      doWithConn(s)
    }
  }

  private def getProp(p: String)(implicit props: java.util.Properties) =
    props.getProperty(p).stripPrefix("\"").stripSuffix("\"")

  private def readPropertiesFile(f: String) = {
    val props = new java.util.Properties
    val in = new java.io.FileInputStream(f)
    props.load(in)
    in.close()
    props
  }

  protected def info  (m: String) { println(m) }
  protected def logErr(m: String) { println("ERROR: " + m) }
}

case class TickerData(currency: String, last: Double)
