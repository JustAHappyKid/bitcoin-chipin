package bitcoinchipin

import scalaj.http.{HttpOptions, Http}
import slick.session.Session
import slick.jdbc.{StaticQuery => Q}

class BaseTask {

  protected def withTransaction[T](s: Session)(action: => T): T = {
    Q.updateNA("BEGIN TRANSACTION")
    val result: T = action
    Q.updateNA("COMMIT TRANSACTION")
    result
  }

  val baseHeaders = List(
    ("User-Agent", "Mozilla/4.0 (compatible; Test client)"))

  def getRequest(theURL: String): String = {
    Http(theURL).headers(baseHeaders).
      option(HttpOptions.connTimeout(3000)).
      option(HttpOptions.readTimeout(5000)).asString
  }

  protected def info  (m: String) { println(m) }
  protected def logErr(m: String) { println("ERROR: " + m) }
}
