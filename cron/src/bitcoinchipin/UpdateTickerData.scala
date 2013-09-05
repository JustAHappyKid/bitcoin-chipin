package bitcoinchipin

import slick.session.Session
import slick.jdbc.{StaticQuery => Q}
import net.liftweb.json.JsonAST.{JDouble, JField, JObject}

object UpdateTickerData extends BaseTask {

  def go(implicit s: Session) {
    val resp = getRequest("http://blockchain.info/ticker")
    val tickers = parseTickerJson(resp)
    info("Got following ticker data:")
    tickers.foreach { t =>
      info(s"${t.currency} is trading at ${t.last} to the 'coin")
      withTransaction(s) {
        Q.update[String]("DELETE FROM ticker_data WHERE currency = ?").execute(t.currency)
        Q.update[(String, Double)]("INSERT INTO ticker_data (currency, last_price) VALUES (?, ?)").
          execute(t.currency, t.last)
      }
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
}

case class TickerData(currency: String, last: Double)