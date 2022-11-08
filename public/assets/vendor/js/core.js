function getParentElement(elementObject, elementName)
{
    if (elementObject.parent().is(elementName)) {
        return elementObject.parent();
    } else {
        return getParentElement(elementObject.parent(), elementName)
    }
}
