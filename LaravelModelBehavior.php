<?php

/**
 * Behavior LaravelModelBehavior
 * Adds functions to a Propel model to be compatible with Laravel Form::model() without the need
 * to create an extra array
 *
 * @author     Stephan Groen <stephan@picqer.com>
 */
class LaravelModelBehavior extends Behavior
{
    public function objectMethods()
    {
        $script = '';
        $script .= $this->addArrayAccessGetter();
        $script .= $this->addArrayAccessSetter();
        $script .= $this->addArrayAccessUnsetter();
        $script .= $this->addArrayAccessIsset();
        $script .= $this->addGetOverload();
        $script .= $this->addIssetOverload();
        return $script;
    }

    public function objectFilter(&$script, $builder)
    {
        $pattern = '/abstract class (\w+) extends (\w+) implements (\w+)/i';
        $replace = 'abstract class ${1} extends ${2}  implements ${3}, ArrayAccess';
        $script = preg_replace($pattern, $replace, $script);
    }

    protected function addArrayAccessIsset()
    {
        return "
public function offsetExists(\$offset)
{
    \$peer = \$this->getPeer();
    if (\$peer::getTableMap()->hasColumnByPhpName(\$offset)) {
        try {
            \$getter = 'get' . \$offset;
            return \$this->\$getter() !== null;
        } catch (PropelException \$ex) {
            return false;
        }
    }

    if (\$peer::getTableMap()->hasColumn(\$offset)) {
        try {
            \$getter = 'get' . \$peer::getTableMap()->getColumn(\$offset)->getPhpName();
            return \$this->\$getter() !== null;
        } catch (PropelException \$e) {
            return false;
        }
    }
}
";
    }

    protected function addArrayAccessGetter()
    {
        return "
public function offsetGet(\$offset)
{
    \$peer = \$this->getPeer();
    if(\$peer::getTableMap()->hasColumnByPhpName(\$offset)) {
        \$getter = 'get' . \$offset;
        return \$this->\$getter();
    } elseif (\$peer::getTableMap()->hasColumn(\$offset)) {
        \$getter = 'get' . \$peer::getTableMap()->getColumn(\$offset)->getPhpName();
        return \$this->\$getter();
    }
    return null;
}
";
    }

    protected function addArrayAccessUnsetter()
    {
        return "
public function offsetUnset(\$offset)
{
    \$peer = \$this->getPeer();
    if(\$peer::getTableMap()->hasColumnByPhpName(\$offset)) {
        try {
            \$setter = 'set' . \$offset;
            return \$this->\$setter();
        } catch (PropelException \$e) {}
    } elseif (\$peer::getTableMap()->hasColumn(\$offset)) {
        try {
            \$setter = 'set' . \$peer::getTableMap()->getColumn(\$offset)->getPhpName();
            return \$this->\$setter();
        } catch (PropelException \$e) {}
    }
}
";
    }

    protected function addArrayAccessSetter()
    {
        return "
public function offsetSet(\$offset, \$value)
{
    \$peer = \$this->getPeer();
    if(\$peer::getTableMap()->hasColumnByPhpName(\$offset)) {
        \$setter = 'set' . \$offset;
        return \$this->\$setter(\$value);
    } elseif (\$peer::getTableMap()->hasColumn(\$offset)) {
        \$setter = 'set' . \$peer::getTableMap()->getColumn(\$offset)->getPhpName();
        return \$this->\$setter(\$value);
    }
}
";
    }

    protected function addGetOverload()
    {
        return "
public function __get(\$property)
{
    \$peer = \$this->getPeer();
    if(\$peer::getTableMap()->hasColumnByPhpName(\$property)) {
        \$getter = 'get' . \$property;
        return \$this->\$getter();
    } elseif (\$peer::getTableMap()->hasColumn(\$property)) {
        \$getter = 'get' . \$peer::getTableMap()->getColumn(\$property)->getPhpName();
        return \$this->\$getter();
    }
}
";
    }

    protected function addIssetOverload()
    {
        return "
public function __isset(\$property)
{
    \$peer = \$this->getPeer();
    if (\$peer::getTableMap()->hasColumnByPhpName(\$property)) {
        try {
            \$getter = 'get' . \$property;
            return \$this->\$getter() !== null;
        } catch (PropelException \$ex) {
            return false;
        }
    }

    if (\$peer::getTableMap()->hasColumn(\$property)) {
        try {
            \$getter = 'get' . \$peer::getTableMap()->getColumn(\$property)->getPhpName();
            return \$this->\$getter() !== null;
        } catch (PropelException \$e) {
            return false;
        }
    }
}
";
    }
}