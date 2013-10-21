
import AssemblyKeys._

assemblySettings

name := "chipin-tasks"

version := "0.2.4"

scalaVersion := "2.10.2"

libraryDependencies ++= Seq(
  "org.scalaj" %% "scalaj-http" % "0.3.7",
  "net.liftweb" %% "lift-json" % "2.5-M4",
  "com.typesafe.slick" %% "slick" % "1.0.1",
  "mysql" % "mysql-connector-java" % "5.1.25"
  //"org.scalatest" %% "scalatest" % "1.9.1" % "test"
)

scalaSource in Compile <<= (baseDirectory in Compile)(_ / "src")

scalaSource in Test <<= (baseDirectory in Test)(_ / "test")

mergeStrategy in assembly <<= (mergeStrategy in assembly) { (old) =>
  {
    case "rootdoc.txt"          => MergeStrategy.concat
    case "META-INF/MANIFEST.MF" => MergeStrategy.discard
    case _                      => MergeStrategy.deduplicate
  }
}
