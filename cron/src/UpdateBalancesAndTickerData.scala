
import slick.session.{Database, Session}

object UpdateBalancesAndTickerData {

  def main(args: Array[String]) {
    withDatabaseConn { implicit s: Session =>
      bitcoinchipin.UpdateAddressBalances.go
      bitcoinchipin.UpdateTickerData.go
    }
  }

  private def withDatabaseConn(doWithConn: (Session) => Unit) {
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
}
