<?php
class TagRemover
{
  private $iterator;
  private $dom = [];

  public function __construct(string $FileName)
  {
    try {
      $this->dom = file($FileName, FILE_SKIP_EMPTY_LINES);
      $this->iterator = new HTMLIterator($this->dom);
    } catch (Exception $a) {
      echo "Файл не читается" . $a . '</br>';
    }
  }

  public function removeTag(string $TagName, string $AttributeName = null, string $AttributeValue = null)
  {
    $this->iterator->rewind();
    do {
      //$currentVal = str_replace(' ', '', $this->iterator->current());
      $currentVal = $this->iterator->current();
      
      if(preg_match("(\<{$TagName})", $currentVal) && preg_match("({$AttributeName}=\"{$AttributeValue}\")", $currentVal)) {
          //echo "Удаляемое значение:" . htmlspecialchars($currentVal) . '</br>';
          array_splice($this->dom, $this->iterator->key(), 1);
       } elseif (preg_match("(\<{$TagName})", $currentVal) && !$AttributeName && !$AttributeValue) {
          //echo "Удаляемое значение:" . htmlspecialchars($currentVal) . '</br>';
          array_splice($this->dom, $this->iterator->key(), 1);
       } else {
          $this->iterator->next();
       }

    } while ($this->iterator->valid());
  }

  public function save(string $FileName = 'user.html')
  {
    file_put_contents($FileName, $this->dom);
  }
}

class HTMLIterator implements Iterator
{
  private $position = 0;
  private $dom = [];

  public function __construct(array &$Dom)
  {
    $this->position = 0;
    $this->dom = &$Dom;
  }

  public function rewind()
  {
    $this->position = 0;
  }

  public function position(int $i)
  {
    $this->position = $i;
  }

  public function current()
  {
    return $this->dom[$this->position];
  }

  public function key()
  {
    return $this->position;
  }

  public function next()
  {
    ++$this->position;
  }

  public function valid()
  {
    return isset($this->dom[$this->position]);
  }
}

//проверка
$domr = new TagRemover('htmlfile.html');
$domr->removeTag('title');
$domr->removeTag('meta', 'name', 'keywords');
$domr->removeTag('meta', 'name', 'description');
$domr->save('htmloutput.html');