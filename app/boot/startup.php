<?php


    chdir(PATH_ROOT);


    use app\classes\GenHTMLController;
    use app\classes\GenHTMLOptions;
    use app\classes\GenHTMLUsage;
    use app\classes\GenHTMLTemplates;
    use app\classes\GenHTMLIndex;


function startup($argv)
    {
    //  If the first command line option is a --help
    //  option then we dump some usage info and
    //  exit.
    //
        if (count($argv) > 1) {
            if ($argv[1] == "--help" || $argv[1] == "--H") {
                $_usage = new GenHTMLUsage($argv);
                return $_usage->isError(false);
            }
        }

    //  Initialise the main controller, this will do
    //  everything for us, we don't need to call on
    //  any other methods.
    //
    //  GenHTMLController will parse the given
    //  arguments vie the GenHTMLOptions class.
    //
    //  Next it will load the HTML and CSS templates
    //  using the GenHTMLTemplates class.
    //
    //  It will then use the GenHTMLIndex class to
    //  load, parse and sort the input files in
    //  preparation for the final build.
    //
    //  Lastly, it will use the GenHTMLBuild class
    //  to complete the process.
    //
    //  Errors are retruned in the $_error_message
    //  member of the GenHTMLController class, all
    //  we need to do it call the isError() method,
    //  this will return false if there are no
    //  errors to report in which case success is
    //  assumed and the HTML documents should be
    //  ready.
    //
        $genHTML = new GenHTMLController($argv);


    //  Return any errors or false on success, see
    //  the isError() and _setError() methods of
    //  GenHTMLController.
    //
    //  See the main GenHTML php script for more
    //  info - it handles any errors from here.
    //
        return $genHTML->isError(false);
    }

