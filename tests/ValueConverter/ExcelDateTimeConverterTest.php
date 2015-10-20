<?php

namespace Ddeboer\DataImport\Tests\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ExcelDateConverter;
use Ddeboer\DataImport\ValueConverter\ExcelDateTimeConverter;

class ExcelDateTimeConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $expected
     * @param $value
     * @dataProvider getDateTimes
     */
    public function testConvertDateTime($expected,$value)
    {
        $converter = new ExcelDateTimeConverter();
        $date = $converter->__invoke($value);
        $this->assertEquals($expected,$date);
    }

    public function getDateTimes()
    {
        return array(
            array(new \DateTime('2015-01-01 09:00:00'),42005.375),
            array(new \DateTime('2087-01-21 06:24:26'),68323.266975458),
            array(new \DateTime('1926-09-12 04:49:07'),9752.2007671141),
            array(new \DateTime('2019-03-18 13:43:15'),43542.5717100276),
            array(new \DateTime('1947-01-11 02:32:41'),17178.106029306),
            array(new \DateTime('1948-02-05 09:40:16'),17568.4029610256),
            array(new \DateTime('1952-01-18 04:41:04'),19011.1951842068),
            array(new \DateTime('1963-02-06 14:53:58'),23048.6208075774),
            array(new \DateTime('1903-10-09 21:40:29'),1378.903111922),
            array(new \DateTime('1974-10-07 13:18:49'),27309.5547441419),
            array(new \DateTime('1962-05-10 04:42:56'),22776.196476063),
            array(new \DateTime('2027-03-10 01:58:59'),46456.0826363282),
            array(new \DateTime('2078-11-15 07:45:31'),65334.3232810246),
            array(new \DateTime('2030-09-26 07:22:16'),47752.3071314071),
            array(new \DateTime('1959-12-19 09:36:28'),21903.400315471),
            array(new \DateTime('1901-09-13 00:46:47'),622.0324823543),
            array(new \DateTime('1932-04-14 02:30:07'),11793.1042418214),
            array(new \DateTime('1924-03-30 17:15:20'),8856.7189809075),
            array(new \DateTime('1914-08-07 01:54:57'),5333.0798159769),
            array(new \DateTime('1935-08-18 12:28:59'),13014.5201175413),
            array(new \DateTime('2073-11-18 00:09:28'),63511.0065851346),
            array(new \DateTime('2021-03-31 14:21:28'),44286.598250463),
            array(new \DateTime('2131-12-22 05:51:35'),84728.2441627219),
            array(new \DateTime('1910-02-08 03:51:32'),3692.1607804476),
            array(new \DateTime('1953-10-02 17:15:20'),19634.7189767405),
            array(new \DateTime('1903-07-31 17:27:30'),1308.7274216342),
            array(new \DateTime('2122-01-01 09:31:41'),81086.3970044698),
            array(new \DateTime('1990-04-16 15:25:23'),32979.6426328965),
            array(new \DateTime('1953-04-16 02:44:08'),19465.1139769427),
            array(new \DateTime('1915-07-29 19:38:36'),5689.8184607353),
            array(new \DateTime('2084-10-23 06:48:48'),67503.283889343),
            array(new \DateTime('2038-07-16 17:08:19'),50602.7141186736),
            array(new \DateTime('2114-11-10 04:16:36'),78477.1782038306),
            array(new \DateTime('2107-11-05 11:25:51'),75915.4762905829),
            array(new \DateTime('2040-10-19 07:35:28'),51428.3163030281),
            array(new \DateTime('2060-09-08 19:23:51'),58692.8082327817));
    }

    /**
     * @dataProvider getDates
     */
    public function testConvertDate($expected,$value)
    {
        $converter = new ExcelDateConverter();
        $date = $converter->__invoke($value);
        $this->assertEquals($expected,$date);
    }

    public function getDates()
    {
        return array(
            array(new \DateTime('1970-01-01'),25569),
            array(new \DateTime('1984-01-01'),30682),
            array(new \DateTime('1984/02/07'),30719),
            array(new \DateTime('1983/06/28'),30495),
            array(new \DateTime('1981/06/01'),29738),
            array(new \DateTime('1989/12/05'),32847),
            array(new \DateTime('1987/10/04'),32054),
            array(new \DateTime('1985/11/21'),31372),
            array(new \DateTime('1984/12/30'),31046),
            array(new \DateTime('1978/04/19'),28599),
            array(new \DateTime('1983/01/10'),30326),
            array(new \DateTime('1985/04/06'),31143),
            array(new \DateTime('1987/02/25'),31833),
            array(new \DateTime('1988/05/30'),32293),
            array(new \DateTime('1983/12/06'),30656),
            array(new \DateTime('1981/06/10'),29747),
            array(new \DateTime('1985/08/13'),31272),
            array(new \DateTime('1980/10/12'),29506),
            array(new \DateTime('1988/08/04'),32359),
            array(new \DateTime('1985/02/27'),31105),
            array(new \DateTime('1984/08/31'),30925),
            array(new \DateTime('1980/09/30'),29494),
            array(new \DateTime('1980/05/09'),29350),
            array(new \DateTime('1989/01/09'),32517),
            array(new \DateTime('1977/07/07'),28313),
        );
    }
}

