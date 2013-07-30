
import java.util.Date
import scalaj.http.{Http, HttpOptions}
import slick.session.{Database, Session}
import slick.jdbc.{StaticQuery => Q}

object UpdateBalances {

  def main(args: Array[String]) {
    withDatabaseConn { implicit s: Session =>
      val addresses = Q.queryNA[String]("SELECT DISTINCT address FROM widgets").list()
      addresses.foreach { a =>
        val resp = getRequest(s"http://blockchain.info/q/addressbalance/$a")
        try {
          val balance: Int = resp.toInt
          println(s"Balance for address $a is $balance.")
          Q.update[String]("DELETE FROM bitcoin_addresses WHERE address = ?").execute(a)
          Q.update[(String, Int, String)](
            "INSERT INTO bitcoin_addresses (address, satoshis, updated_at) VALUES (?, ?, ?)").
            execute(a, balance, now)
        } catch {
          case e: java.lang.NumberFormatException =>
            println("ERROR: Got non-integer response: " + resp)
        }

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
}
