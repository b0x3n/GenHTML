<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  Option keys used to index the OPTION_LONG and
 |  OPTION_SHORT arrays.
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
    __defifndef('OPTION_USAGE',         'option_usage');


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
 |  We now define a whole bunch of arrays that are
 |  linked by index:
 |
 |    USAGE_SHORT
 |    USAGE_SYNOPSIS
 |    USAGE_LONG
 |
 |  And so on - these are used by the _usage() method in
 |  GenHTMLUsage to dump information about a specific
 |  option.
 |
 |  Everything is cross-referenced via index.
 |
 */
    __defifndef('USAGE_SHORT', Array(
        "Specify the index path directory",
        "Specify the output path directory",
        "Specify the HTML template location",
        "Specify the CSS template location",
        "Specify the HTML language",
        "Specify the link/section depth",
        "Build a multi-file HTML document",
        "Suppress output (except errors)",
        "Get help using GenHTML"
    ));


    __defifndef('USAGE_SYNOPSIS',
"GenHTML 1.0

 Usage:

   php GenHTML [--option <paramter>] [-switch]


 Description

  GenHTML allows you to quickly generate HTML
  documentation from raw text files without any
  markup or extensive formatting.
 
  The application is written in php, so must be
  executed by the php interpreter:

    php GenHTML
 
  For detailed information on how to use GenHTML
  check out the documentation in:

    GenHTML/doc/HTML/


 Options

  Command line options come in two forms, there
  are --options which always require a parameter,
  and -switches which do not.

  There are also long and short versions of each,
  long are clearly readable but longer to type,
  short more terse but harder to read, for example:

    php GenHTML --input-path txt --output-path doc -multi-page -silent

  Is the same as:

    php GenHTML --i txt --o doc -m -s


 Available options and switches:

 ");


    __defifndef('USAGE_FOOTER',
"
  For help on a particular option, use --help or
  rhe shorter --h option, example:

    php GenHTML --help --input-path

  Will output information about the --input-path
  option.
  
");


    __defifndef('USAGE_LONG', Array(
"  GenHTML requires a single input file, this is
  the main index text file which may contain an
  index of other input files to be included.

  By default, GenHTML will look in the current
  working directory for a file named index, or
  we can specify a particular file using the
  --index-path or shorter --i option:

    php GenHTML --input-path txt/index

  In this case, GenHTML will expect to find the
  main index file in the directory named txt.

  This also changes the current working path,
  if the index includes a list of external
  input files then the paths are relative to
  txt and not the current working directory.

  See the documentation in:

    GenHTML/doc/HTML

  For more detailed information on how to write
  an index file.
",
"  The --output-path or shorter --o options are
  used to specify an output path, this is where
  the output HTML and CSS files are written.
  
    php GenHTML --output-path docs
    
  Tells GenHTML to write the output files to a
  directory called docs. If the directory does
  not exist it will be created.

  By default, the GenHTML application will write
  to a directory named html if no output path is
  specified at the command line.
",
"  GenHTML uses a set of pre-defined templates
  to build HTML documents. By default, GenHTML
  will use its own set of templates if none are
  specified at the command line. These are found
  in:

    GenHTML/Templates/HTML/

  Or we can specify a custom template set:

    php GenHTML --html-path my_templates

  There are two ways we can supply templates, in
  individual files or a single file format.

  GenHTML will determine this automatically.

  Check out the documentation in:

    GenHTML/doc/HTML/

  If you want to know more about HTML templates.
",
"  GenHTML requires a CSS file to build the HTML
  pages.

  If no CSS file is specified at the command line
  then GenHTML will use the default file:

    GenHTML/Templates/CSS/main.css

  If you want to use a custom CSS file, you can
  specify it at the command line using either the
  --css-path or --c options:

    php GenHTML --css-path main.css

  The file must be named main.css - see the
  documentation in:

    GenHTML/doc/HTML/

  For more info.
",
"  The HTML language is set to en (English) by
  default.

  This is inserted into the document <html> tag
  like:

    <html lang=\"en\">

  If you need to change this, you can use the
  --html-language or shorter --l options:

    php GenHTML --html-language ru

  This assumes that the PAGE_TOP template has
  not been altered or includes the {{PAGE_TOP}}
  directive - for more info see the documentation
  in:

    GenHTML/doc/HTML/

",
"  The --link-depth and shorter --d options are
  used to specify the detph of the navigation
  lines and sections.

  By default this is set to 3, which means that
  links will be built to a depth of 3. We can
  set it to either 1, 2 or 3:

    php GenHTML --link-depth 1

  In this case, GenHTML will only generate links
  for pages, but not sub-sections within those
  pages.

  Check the documentation in:

    GenHTML/doc/HTML/

  For more detailed information.
",
"  By default the GenHTML application will create
  a single page HTML document - all of the input
  files will be compiled into a single page and
  the pages and sections linked internally.

  If we want to create a multi-page document, we
  use the -multi-page or shorter -m switches:

    php GenHTML -m

  This means that, for each input file a single
  HTML document will be created, and that each of
  the documents will be linked externally.

  See the documentation in:

    GenHTML/doc/HTML/

  For more detailed information.
",
"  By default, GenHTML will dump a lot of data to
  the terminal describing the build.

  This output can be suppressed using either the
  -silent or the shorter -s scwitches:

    php GenHTML --silent

  This will, of course - not prevent GenHTML from
  displaying error messages should any issues
  occur with the build.
",
"  The --help option is used to get information 
  about the available GenHTML command line options.

  If we use the --help or the shorter --H options
  without a parameter, we will get a list of all
  available options and some other information:

    php GenHTML --help

  If we want help using a particular option, we
  must specify that option as a parameter to the
  --help option:

    php GenHTML --help --input-path

  For example, will display information about the
  --input-path or --i options.

  For more detailed information about GenHTML see
  the documentation in:
  
    GenHTML/doc/HTML/
"
    ));


/*---------------------------------------------------------
 |
 |  GenHTMLUsage
 |
 |  This is the only GenHTML class that isn't initialised
 |  by the main controller, therefore any errors are
 |  reported vie the local isError() method.
 |
 |  Nothing much going on here, it's all info and echo's,
 |  just a class to help get information about GenHTML
 |  and the available command line options.
 |
 */
Class GenHTMLUsage
{

    private         $_error_message;


public function __construct($argv)
    {
        $this->_error_message = false;

        if (! isset($argv[2]))
    //  No specific option - dump all options and
    //  synopsis.
    //
            $this->_dump();
        else
    //  Dump help for specific option.
    //
            $this->_usage($argv[2]);
    }


//  Outputs help on the specified option if it
//  exists.
//
//  If not sets the local $_error_message.
//
private function _usage($option)
    {
        $_options = Array(
            OPTION_INDEX_PATH,
            OPTION_OUTPUT_PATH,
            OPTION_HTML_PATH,
            OPTION_CSS_PATH,
            OPTION_LANGUAGE,
            OPTION_LINK_DEPTH,
            OPTION_MULTI_PAGE,
            OPTION_SILENT
        );

        $_description = false;

        $_long = "--help";
        $_short = "--H";

        if ($option == "--help" || $option == "--H")
            $_description = USAGE_LONG[(count(USAGE_LONG) - 1)];
        else {
            foreach (OPTION_LONG as $index=>$_long) {
                $_short = OPTION_SHORT[$index];

                if ($option == $_short || $option == $_long) {
                    $_key = array_search($index, $_options);

                    if ($_key !== false)
                        $_description = USAGE_LONG[$_key];
                    break;
                }
            }
        }

        if ($_description === false)
            return $this->_setError("Unknown option: " . $option);
  
        echo "GenHTML ($option)\n\n\n";

        if (substr($option, 0, 2) == "--")
            echo " Usage:\n\n   php GenHTML $option <parameter>\n\n\n";
        else
            echo " Usage:\n\n   php GenHTML $option\n\n\n";

        echo " Variations:\n\n";
        echo "  Long:  $_long\n  Short: $_short\n\n\n";
        echo " Description:\n\n";

        echo $_description . "\n";

        return true;
    }


//  Dump full synopsis and all available options
//  and switches.
//
private function _dump()
    {
        $_index = 0;

        echo USAGE_SYNOPSIS;

            echo "  Long              Short   Description\n\n";

        foreach (OPTION_LONG as $index=>$option) {
            if ($_index == 6) {
                echo "    --help\033[12C";
                echo "--h     ";
                echo USAGE_SHORT[(count(USAGE_SHORT) - 1)] . "\n";
            }

            $_pad = (18 - strlen($option));

            echo "    $option\033[{$_pad}C";

            $_pad = (8 - strlen(OPTION_SHORT[$index]));
            echo OPTION_SHORT[$index] . "\033[{$_pad}C";
            echo USAGE_SHORT[$_index++] . "\n";
        }

        echo USAGE_FOOTER;
    }


private function _setError($error_message = false)
    {
        $this->_error_message = $error_message;
        return false;
    }


public function isError($report_error = true)
    {
        if ($this->_error_message !== false) {
            if ($report_error === true)
                echo $this->_error_message;
        }

        return $this->_error_message;
    }

}
