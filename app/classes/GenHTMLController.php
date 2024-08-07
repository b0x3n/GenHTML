<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  GenHTMLController
 |
 |  Main application controller.
 |
 |  All that needs done, is to pass the command line
 |  arguments to the constructor. The constructor will
 |  then create the other objects required for the
 |  final build.
 |
 |  Once the controller returns - all the caller needs
 |  to do is call the isError() method, this will return
 |  an error message if any errors occurred with the
 |  build, or false if the build was successful.
 |
 */
Class GenHTMLController
{

    static          $_error_message;

    public          $_options;
    public          $_templates;
    public          $_index;


public function __construct($argv)
    {
        $this->_setError();

    //  Sort the command line options, generating the
    //  _options object where all of the options are
    //  stored.
    //
    //  See GenHTMLOptions.php for more info.
    //
        $this->_options = new GenHTMLOptions($argv);
        if ($this->isError(false) !== false)
            return;

    //  Load the CSS and HTML template files, this
    //  instantiates the _templates object where the
    //  template data is stored.
    //
    //  See GenHTMLTemplates.php for more info.
    //
        $this->_templates = new GenHTMLTemplates($this->_options);
        if ($this->isError(false) !== false)
            return;

    //  Load all of the input files - this creates
    //  the _index object where all of the parsed input
    //  files are stored for the final build.
    //
    //  See GenHTMLIndex.php for more info.
    //
        $this->_index = new GenHTMLIndex($this->_options, $this->_templates);
        if ($this->isError(false) !== false)
            return;

    //  Finally - the build.
    //
    //  See GenHTMLBuild.php for more info.
    //
        $this->_build = new GenHTMLBuild($this->_options, $this->_templates, $this->_index);
    }


protected function _setError($error_message = false)
    {
        self::$_error_message = $error_message;
        return false;
    }


public function isError($report_error = true)
    {
        $_error_message = self::$_error_message;

        if ($_error_message !== false) {
            if ($report_error)
                echo $_error_message . "\n";
        }

        return $_error_message;
    }

}

