<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  Template key indexes - these are used to index the
 |  HTML templates in the _html array within the
 |  GenHTMLTemplates class.
 |
 */
    __defifndef('TEMPLATE_KEY', Array(
        'PAGE_OPEN'         =>  'PAGE_OPEN',
        'PAGE_HEADER'       =>  'PAGE_HEADER',
        'PAGE_SECTION'      =>  'PAGE_SECTION',
        'PAGE_FOOTER'       =>  'PAGE_FOOTER',
        'PAGE_CLOSE'        =>  'PAGE_CLOSE',
        'SECTION_TOP'       =>  'SECTION_TOP',
        'SECTION_HEADER'    =>  'SECTION_HEADER',
        'SECTION_SUBHEADER' =>  'SECTION_SUBHEADER',
        'SECTION_PARAGRAPH' =>  'SECTION_PARAGRAPH',
        'SECTION_CODEBLOCK' =>  'SECTION_CODEBLOCK',
        'SECTION_BOTTOM'    =>  'SECTION_BOTTOM'
    ));


/*---------------------------------------------------------
 |
 |  GenHTMLTemplates
 |
 |  Used to load and store the CSS and HTML template
 |  files.
 |
 |  Like all other objects, we need only instantiate
 |  the object, the constructor does everything and
 |  will indicate errors via the $_error_message in
 |  the parent controller.
 |
 */
Class GenHTMLTemplates extends GenHTMLController
{

    public          $_html;
    public          $_css;
    

public function __construct($options)
    {
        $this->_html = Array();
        $this->_css = false;

    //  Loading HTML templates from a file or
    //  directory?
    //
        if (is_dir($options->_html_path))
            $this->_loadHTMLTemplateDir($options->_html_path);
        else
            $this->_loadHTMLTemplateFile($options->_html_path);

        if (parent::isError(false))
            return;

        if ($this->_checkTemplates() === false)
            return;

        if ($this->_loadCSSTemplate($options->_css_path) === false)
            return;

        if ($options->_silent === false)
            $this->_dump();
    }


//  Load HTML template files from $path.
//
private function _loadHTMLTemplateDir($path)
    {
        if (($_dir = opendir($path)) === NULL)
            return parent::_setError("_loadHTMLTemplateDir(): HTML directory " . $path . " could not be opened");

        while ($_entry = readdir($_dir)) {
            $_key = str_replace('-', '_', strtoupper($_entry));

            if (array_search($_key, TEMPLATE_KEY) === false)
                continue;

            $_path = __buildpath(Array($path, $_entry));

            $this->_html[$_key] = file_get_contents($_path);
        }

        closedir($_dir);

        return true;
    }


//  Add an HTML template to the array.
//
private function _addHTMLTemplate($key, $lines)
    {
        if (array_search($key, TEMPLATE_KEY) === false)
            return parent::_setError("_addHTMLTemplate(): Undefined template key " . $key);

        $_template = false;

        foreach ($lines as $line) {
            if ($_template === false) $_template = $line;
            else $_template .= "\n" . $line;
        }

        if ($_template === false || empty(trim($_template)))
            return parent::_setError("_addHTMLTemplate(): The " . $key . " template has no data");

        $this->_html[$key] = $_template;

        return true;
    }


//  Every template is in an individual file in
//  the $template_path.
//
private function _loadHTMLTemplateFile($template_path)
    {
        if (($_stream = fopen($template_path, "r")) === false)
            return parent::_setError("_loadHTMLTemplateFile(): Cannot find HTML templte file " . $template_path);
    
        $_lines = Array();
        $_key = false;

        while (! feof($_stream)) {
            $_line_in = fgets($_stream);

        //  Collect every line up to the next blank
        //  line.
        //
            if (empty(trim($_line_in)) || trim($_line_in) == "\n") {
                if ($_key !== false) {
                    if ($this->_addHTMLTemplate($_key, $_lines) === false) {
                        fclose($_stream);
                        return false;
                    }

                    $_key = false;
                    $_lines = Array();
                }

                continue;
            }

            if ($_key === false)
                $_key = trim($_line_in);
            else
                array_push($_lines, $_line_in);
        }

        fclose($_stream);

        if ($_key !== false) {
            if ($this->_addHTMLTemplate($_key, $_lines) === false)
                return false;
        }

        return true;
    }


//  Checks that all HTML templates were loaded
//  successfully.
//
private function _checkTemplates()
    {
        foreach (TEMPLATE_KEY as $key) {
            if (! isset($this->_html[$key]))
                return parent::_setError("_checkTemplates(): Template " . $key . " is not defined");
            
            $_html = $this->_html[$key];

            if (empty(trim($_html)) || trim($_html) == "\n")
                return parent::_setError("_checkTemplates(): " . $key . " template contains no data");
        }

        return true;
    }


//  The CSS template is really just a single
//  .css file.
//
private function _loadCSSTemplate($path)
    {
        if (! is_file($path))
            return parent::_setError("_loadCSSTemplate(): Cannot find CSS tmeplate file " . $path);

        $this->_css = file_get_contents($path);

        return true;
    }


private function _dump()
    {
        echo "Loaded " . count($this->_html) . " HTML templates:\n\n";
        foreach ($this->_html as $key=>$template)
            echo " $key (" . strlen($template) . " bytes)\n";
        echo "\n";
        echo "Loaded CSS template file (" . strlen($this->_css) . " bytes)\n\n";
    }

}

