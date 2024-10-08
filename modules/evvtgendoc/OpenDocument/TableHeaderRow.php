<?php
/**
* OpenDocument_TableHeaderRow class
*
*  This file is based on the work done by Alexander Pak <irokez@gmail.com>
*  in his OpenDocument Library distributed on PEAR.
*  A special thanks to him for setting the road we followed.
*  This library is governed by the GNU Lesser Public License
*
* The whole library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*
* @category   File Formats
* @package    OpenDocument
* @author     Joe Bordes, JPL TSolucio, S.L. <joe@tsolucio.com>
* Copyright 2009 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
*/

require_once 'Element.php';

/**
* OpenDocument_TableHeaderRow element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_TableHeaderRow extends OpenDocument_StyledElement {

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	public $children;

	/**
	 * Node namespace
	 */
	const nodeNS = OpenDocument::NS_TABLE;

	/**
	 * Node amespace
	 */
	const nodePrefix = 'table';

	/**
	 * Node name
	 */
	const nodeName = 'table-header-rows';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'T';

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document) {
		parent::__construct($node, $document);
		$this->allowedElements = array(
			'OpenDocument_TableCoveredCell',
			'OpenDocument_TableCell',
			'OpenDocument_TableRow',
		);
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_TableHeaderRow
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object) {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_TableHeaderRow($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document);
		$node->appendChild($element->node);

		return $element;
	}

	/**
	 * Get style information
	 *
	 * @param array $properties
	 * @return array
	 */
	public function getStyle($properties) {
		return $this->document->getStyle($this->getStyleName(), $properties);
	}

	/**
	 * Get style name
	 *
	 * @return string
	 */
	public function getStyleName() {
		return $this->node->getAttributeNS(OpenDocument::NS_TABLE, 'style-name');
	}

	/**
	 * Get style name prefix
	 *
	 * @return string
	 */
	public function getStyleNamePrefix() {
		return $this->styleNamePrefix;
	}

	/**
	 * Generate new style name
	 *
	 * @return string $stylename
	 */
	public function generateStyleName() {
		self::$styleNameMaxNumber ++;
		return self::styleNamePrefix . self::$styleNameMaxNumber;
	}

	/**
	 * Apply style information
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function applyStyle($name, $value, $elemtype) {
		$style_name = $this->node->getAttributeNS(OpenDocument::NS_TABLE, 'style-name');
		$style_name = $this->document->applyStyle($style_name, $name, $value, $this, $elemtype);
		$this->node->setAttributeNS(OpenDocument::NS_TABLE, 'style-name', $style_name);
	}
	/************** Elements ****************/

	/**
	 * Create OpenDocument_TableRow
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableRow
	 * @access public
	 */
	public function createTableRow($text = '') {
		return OpenDocument_TableRow::instance($this);
	}

	/**
	 * Create OpenDocument_TableCell
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableCell
	 * @access public
	 */
	public function createTableCell($text = '', $colspan = '', $rowspan = '') {
		return OpenDocument_TableCell::instance($this, $text, $colspan, $rowspan);
	}

	/**
	 * Create OpenDocument_TableCoveredCell
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableCoveredCell
	 * @access public
	 */
	public function createTableCoveredCell() {
		return OpenDocument_TableCoveredCell::instance($this);
	}

	/**
	 * Get children list
	 *
	 * @return ArrayIterator
	 * @access public
	 */
	public function getChildren() {
		if (empty($this->children)) {
			$this->listChildren();
		}
		return $this->children->getIterator();
	}
}
?>
