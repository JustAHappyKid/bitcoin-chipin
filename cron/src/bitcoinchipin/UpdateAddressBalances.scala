package bitcoinchipin

import slick.session.Session
import slick.jdbc.{StaticQuery => Q}
import java.util.Date

object UpdateAddressBalances extends BaseTask {

  def go(implicit s: Session) {
    val addresses = Q.queryNA[String]("SELECT DISTINCT address FROM widgets").list()
    addresses.foreach { a =>
      try {
        info(s"Requesting balance for address $a...")
        val resp = getRequest(s"http://blockchain.info/q/addressbalance/$a")
        try {
          val balance: Int = resp.toInt
          info(s"  Balance for address $a is $balance.")
          withTransaction(s) {
            Q.update[String]("DELETE FROM bitcoin_addresses WHERE address = ?").execute(a)
            Q.update[(String, Int, String)](
              "INSERT INTO bitcoin_addresses (address, satoshis, updated_at) VALUES (?, ?, ?)").
              execute(a, balance, now)
          }
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

  private def now: String = {
    val df = new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
    df.format(new Date)
  }
}
