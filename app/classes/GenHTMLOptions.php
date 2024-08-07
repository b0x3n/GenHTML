<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  Opion key definitions - these are used to index the
 |  long and short versions of the command line options.
 |
 */
    __defifndef('OPTION_INDEX_PATH',    'option_index_path');
    __defifndef('OPTION_OUTPUT_PATH',   'option_output_path');
    __defifndef('OPTION_HTML_PATH',     'option_html_path');
    __defifndef('OPTION_CSS_PATH',      'option_css_path');
    __defifndef('OPTION_LANGUAGE',      'option_language');
    __defifndef('OPTION_LINK_DEPTH',    'option_link_depth');
    __defifndef('OPTION_MULTI_PAGE',    'option_multi_page');
    __defifndef('OPTION_SILENT',        'option_silent');


    __defifndef('OPTION_LONG', Array(
        OPTION_INDEX_PATH       =>      '--index-path',
        OPTION_OUTPUT_PATH      =>      '--output-path',
        OPTION_HTML_PATH        =>      '--html-path',
        OPTION_CSS_PATH         =>      '--css-path',
        OPTION_LANGUAGE         =>      '--language',
        OPTION_LINK_DEPTH       =>      '--link-depth',
        OPTION_MULTI_PAGE       =>      '-multi-page',
        OPTION_SILENT           =>      '-silent'
    ));


    __defifndef('OPTION_SHORT', Array(
        OPTION_INDEX_PATH       =>      '--i',
        OPTION_OUTPUT_PATH      =>      '--o',
        OPTION_HTML_PATH        =>      '--h',
        OPTION_CSS_PATH         =>      '--c',
        OPTION_LANGUAGE         =>      '--l',
        OPTION_LINK_DEPTH       =>      '--d',
        OPTION_MULTI_PAGE       =>      '-m',
        OPTION_SILENT           =>      '-s'
    ));


/*---------------------------------------------------------
 |
 |  GenHTMLOptions
 |
 |  This class processes the command line options and
 |  stores them in a local configuration for the final
 |  build.
 |
 |  Again - only the constructor needs called, here. It
 |  will set the $_error_message in the main controller
 |  if there are any issues.
 |
 */
Class GenHTMLOptions extends GenHTMLController
{

    public          $_input_path;
    public          $_index_path;

    public          $_output_path;

    public          $_html_path;
    public          $_css_path;


    public          $_language = "en";
    public          $_title = false;
    public          $_link_depth;

    public          $_open_list;
    public          $_close_list;


    public          $_multi_page;
    public          $_silent;


public function __construct($argv)
    {
    //  Initialise everything with default values
    //  before processing the options.
    //
        $this->_input_path = PATH_ROOT;
        $this->_index_path = "index";

        $this->_output_path = "html";
        
        $this->_html_path = __buildpath(Array(PATH_ROOT, "Templates", "HTML"));
        $this->_css_path = __buildpath(Array(PATH_ROOT, "Templates", "CSS", "main.css"));

        $this->_link_depth = 3;

        $this->_open_list = "<ol>";
        $this->_close_list = "</ol>";

        $this->_multi_path = false;
        $this->_silent = false;

    //  Finally, call the _processOptions() method.
    //  
        if ($this->_processOptions($argv) === false)
            return;

        $this->dump();
    }


private function _isOption($name, $key)
    {
        if (strtolower($name) == OPTION_LONG[$key])
            return true;
        if (strtolower($name) == OPTION_SHORT[$key])
            return true;

        return false;
    }


private function _setOption($argv, &$arg, &$dst)
    {
        if (($arg + 1) >= count($argv))
            return parent::_setError("_setOption(): Option " . $argv[$arg] . " requires a parameter");

        $dst = $argv[++$arg];

        return true;
    }


private function _processOptions($argv)
    {
        for ($arg = 1; $arg < count($argv); $arg++) {
            $_arg = $argv[$arg];

        //  -- prefixed options require a paramter,
        //  the _setOption() method is used here to
        //  check and set.
        //
            if ($this->_isOption($_arg, OPTION_INDEX_PATH)) {
                if (! $this->_setOption($argv, $arg, $this->_index_path))
                    return;
                continue;
            }
            if ($this->_isOption($_arg, OPTION_OUTPUT_PATH)) {
                if (! $this->_setOption($argv, $arg, $this->_output_path))
                    return;
                continue;
            }
            if ($this->_isOption($_arg, OPTION_HTML_PATH)) {
                if (! $this->_setOption($argv, $arg, $this->_html_path))
                    return;
                continue;
            }
            if ($this->_isOption($_arg, OPTION_CSS_PATH)) {
                if (! $this->_setOption($argv, $arg, $this->_css_path))
                    return;
                continue;
            }
            if ($this->_isOption($_arg, OPTION_LANGUAGE)) {
                if (! $this->_setOption($argv, $arg, $this->_language))
                    return;
                continue;
            }
            if ($this->_isOption($_arg, OPTION_LINK_DEPTH)) {
                if (! $this->_setOption($argv, $arg, $this->_link_depth))
                    return;
                continue;
            }

        //  -switches don't require a parameter.
        //
            if ($this->_isOption($_arg, OPTION_MULTI_PAGE)) 
                $this->_multi_page = true;
            else if ($this->_isOption($_arg, OPTION_SILENT)) 
                $this->_silent = true;
            else
                return parent::_setError("_processOptions(): Unknown option " . $_arg);
        }

        return $this->_checkOptions();
    }


private function _checkOptions()
    {
        $_pathinfo = pathinfo($this->_index_path);

    //  The base directory path of the _index_path
    //  becomes the _input_path
    //
        if (isset($_pathinfo['dirname']) && isset($_pathinfo['filename'])) {
            $this->_input_path = $_pathinfo['dirname'];
            $this->_index_path = $_pathinfo['filename'];
        }

        if ($this->_link_depth < 1 || $this->_link_depth > 3)
            return parent::_setError("The --link-depth option must be 1, 2 or 3");

        return true;
    }


public function dump()
    {
        if ($this->_silent === false) {
            echo "Input path:     " . $this->_input_path . "\n";
            echo "Index path:     " . $this->_index_path . "\n\n";

            echo "Output path:    " . $this->_output_path . "\n\n";

            echo "HTML path:      " . $this->_html_path . "\n";
            echo "CSS path:       " . $this->_css_path . "\n\n";

            if ($this->_multi_page)
                echo "Creating multi-page HTML document.\n\n";
            else
                echo "Creating single-page HTML document.\n\n";
        }
    }

}

