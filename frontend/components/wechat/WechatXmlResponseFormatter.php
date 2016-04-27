<?php
namespace components\wechat;

use Yii;
use DOMDocument;
use yii\web\XmlResponseFormatter;
use DOMElement;
use DOMText;
use yii\helpers\StringHelper;
use yii\base\Arrayable;
use DOMCdataSection;


class WechatXmlResponseFormatter extends XmlResponseFormatter{
  public $rootTag = "xml";  // 这里我就可以把 rootTag 的默认值修改成 xml 了
  /**
   * 如果需要使用 CDATA 那就需要把原来的数据转成数组，并且数组含有以下key
   * ，我们就把这个节点添加成一个 DOMCdataSection
   */
  const CDATA = '---cdata---';  // 这个是是否使用CDATA 的下标

  /**
   * 创建微信格式的XML
   * @param array $data
   * @param null $charset
   * @return string
   */
  public function xml(array $data, $charset = null)
  {
    $dom = new DOMDocument('1.0', $charset === null ? Yii::$app->charset : $charset);
    $root = new DOMElement('xml');
    $dom->appendChild($root);
    $this->buildXml($root, $data);
    $xml = $dom->saveXML();
    return trim(substr($xml, strpos($xml, '?>') + 2));
  }
  /**
   * @param DOMElement $element
   * @param mixed $data
   */
  protected function buildXml($element, $data)
  {
    if (is_array($data) ||
        ($data instanceof \Traversable && $this->useTraversableAsArray && !$data instanceof Arrayable)
    ) {
      foreach ($data as $name => $value) {
        if (is_int($name) && is_object($value)) {
          $this->buildXml($element, $value);
        } elseif (is_array($value) || is_object($value)) {
          $child = new DOMElement(is_int($name) ? $this->itemTag : $name);
          $element->appendChild($child);
          if(array_key_exists(self::CDATA, $value)){
            $child->appendChild(new DOMCdataSection((string) $value[0]));
          }else{
            $this->buildXml($child, $value);
          }
        } else {
          $child = new DOMElement(is_int($name) ? $this->itemTag : $name);
          $element->appendChild($child);
          $child->appendChild(new DOMText((string) $value));
        }
      }
    } elseif (is_object($data)) {
      $child = new DOMElement(StringHelper::basename(get_class($data)));
      $element->appendChild($child);
      if ($data instanceof Arrayable) {
        $this->buildXml($child, $data->toArray());
      } else {
        $array = [];
        foreach ($data as $name => $value) {
          $array[$name] = $value;
        }
        $this->buildXml($child, $array);
      }
    } else {
      $element->appendChild(new DOMText((string) $data));
    }
  }
}