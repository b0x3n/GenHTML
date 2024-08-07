<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  GenHTMLBuild
 |
 |  This is the last object that the controller calls
 |  after it has processed command line options, loaded
 |  all templates and parsed and sorted input files.
 |
 |  Will create the output HTML files.
 |
 |  Like other objects - no methods are required by
 |  the called, the constructor manages everything and
 |  the $_error_message is set in the parent object
 |  if there are any issues.
 |
 */
Class GenHTMLBuild extends GenHTMLController
{
//  Most of these just store copies of the
//  GenHTMLOptions config object.
//
    public          $_title;
    public          $_language;
    public          $_link_depth;

    public          $_open_list;
    public          $_close_list;

    public          $_output_path;
    public          $_multi_page;
    public          $_silent;

    public          $_html_template;
    public          $_css_template;


//  These are used to generate the links and
//  pages for the build.
//
    public          $_file_name;
    public          $_pages;
    public          $_links;

    public          $_document;


public function __construct($options, $templates, $index)
    {
        $this->_title = $options->_title;
        $this->_language = $options->_language;
        $this->_link_depth = $options->_link_depth;

        $this->_open_list = $options->_open_list;
        $this->_close_list = $options->_close_list;

        $this->_output_path = $options->_output_path;
        $this->_multi_page = $options->_multi_page;
        $this->_silent = $options->_silent;

        $this->_html_template = $templates->_html;
        $this->_css_template = $templates->_css;

        $this->_file_name = $index->_file_name;
        $this->_pages = $index->_page;
        $this->_links = $index->_link;

        $this->_document = Array();

        $this->_build();

        if (parent::isError(false) !== false)
            return;

        if ($this->_silent === false)
            echo "Done, you can now view " . __buildpath(Array($this->_output_path, "index.html")) . " in your browser.\n";
    }


//  Returns the expanded PAGE_OPEN HTML template.
//
private function _getPageOpen($index, $links, $pages)
    {
        $_templates = $this->_html_template;
      
        $_pathinfo = pathinfo($this->_file_name[$index]);
        $_page_name = $_pathinfo['filename'];

        if ($index == 0)
            $_page_title = $this->_title . " - Index";
        else 
            $_page_title = $this->_title . " - " . $_page_name;

        $_page_open = str_replace(
            '{{PAGE_LANGUAGE}}', 
            $this->_language, 
            $_templates[TEMPLATE_KEY['PAGE_OPEN']]
        );

        $_page_open = str_replace(
            '{{PAGE_TITLE}}', 
            $_page_title,
            $_page_open
        );

        $_page_open .= str_replace(
            '{{PAGE_TITLE}}', 
            $this->_title,
            $_templates[TEMPLATE_KEY['PAGE_HEADER']]
        );

        return $_page_open;
    }


//  Returns the expanded PAGE_CLOSE HTML template.
//
private function _getPageClose($index, $links, $pages)
    {
        $_templates = $this->_html_template;
     
        $_page_close = $_templates[TEMPLATE_KEY['PAGE_FOOTER']];
        $_page_close .= $_templates[TEMPLATE_KEY['PAGE_CLOSE']];

        return $_page_close;
    }


//  Returns the expanded PAGE_TOP and PAGE_BOTTOM
//  templates.
//
private function _getPageNav(
        $index, $links, $pages, $section, $is_single = false
    )
    {
        if ($is_single === true)
            return "\n<div class=\"empty-div\">&nbsp;</div>\n";

        $_templates = $this->_html_template;
        
        $_pathinfo = pathinfo($this->_file_name[$index]);
        $_file_name = $_pathinfo['filename'];

        $_prev_path = "";
        $_next_path = "";

        $_prev = "&nbsp;";
        $_next = "&nbsp;";

        $_replace = "{{PAGE_INDEX}}";
        $_replace_str = "&nbsp;";

        if ($index > 0) {   
            $_pathinfo = pathinfo($this->_file_name[($index - 1)]);
            $_prev_path = $_pathinfo['filename'];

            if ($index != 1)
                $_prev = "<a href=\"{$_prev_path}.html\">" . str_replace('-', ' ', $_prev_path) . "</a>";
            else
                $_prev = "<a href=\"{$_prev_path}.html\">" . $this->_title . "</a>";
        }    

        if ($index < (count($pages) - 1)) {
            $_pathinfo = pathinfo($this->_file_name[($index + 1)]);
            $_next_path = $_pathinfo['filename'];
            
            $_next = "<a href=\"{$_next_path}.html\">" . str_replace('-', ' ', $_next_path) . "</a>";
        }

        $_page_nav = str_replace(
            '{{PAGE_PREV}}',
            $_prev,
            $_templates[TEMPLATE_KEY[$section]]
        );

        $_page_nav = str_replace(
            '{{PAGE_NEXT}}',
            $_next,
            $_page_nav
        );

        if ($section == TEMPLATE_KEY['SECTION_TOP']) {
            if ($index > 1)
                $_replace_str = "<a href=\"{$this->_file_name[0]}.html\">" . $this->_title . "</a>";
        }
        else {
            $_replace = "{{PAGE_TOP}}";
            $_replace_str = "<a href=\"#top\">Top</a>";
        }

        $_page_nav = str_replace(
            $_replace,
            $_replace_str,
            $_page_nav
        );

        return $_page_nav;
    }


//  Builds the navigation links for each of the
//  pages.
//
//  This method is huge and a bit of a hack job
//  to be fair, most of this class is and requires
//  extensive re-factoring.
//
//  I'll get to it in the next iteration or
//  version.
//
private function _getNavLinks(
        $index, $links, $pages, $is_single = false
    )
    {
        $_open_list = $this->_open_list;
        $_close_list = $this->_close_list;

        $_nav = "   $_open_list\n";

        $_link_depth = $this->_link_depth;
        $_depth = 0;

        $_empty_li = "<li class=\"empty-li\">";

        foreach ($links as $_index=>$link_array) {
            if ($index != 0 && $_index != $index)
                continue;

            $_links = $link_array;

            $_pathinfo = pathinfo($this->_file_name[$_index]);
            $_page_name = $_pathinfo['filename'];
    
            foreach ($_links as $link) {
                if (substr($link, 0, 1) == "_") {
                    if (substr($link, 1, 1) == "_") {
                        if ($_depth == 0) {
                            $_nav .= "    $_empty_li\n    $_open_list\n    $_empty_li\n    $_open_list\n";
                            $_depth = 2;
                        } else if ($_depth == 1) {
                            $_nav .= "     $_empty_li\n     $_open_list\n";
                            $_depth = 2;
                        }

                        $_link = str_replace('_', ' ', substr($link, 2));
                        $link = substr($link, 2);
                
                        if ($_link_depth < 3) continue;
                        if ($is_single === false)
                            $_page_link = "     <li><a href=\"{$_page_name}.html#__$link\">$_link</a></li>\n";
                        else
                            $_page_link = "     <li><a href=\"#__$link\">$_link</a></li>\n";
                        $_nav .= $_page_link;
                    } else {
                        if ($_depth < 1) {
                            $_nav .= "    $_empty_li\n    $_open_list\n";
                            $_depth++;
                        } else if ($_depth > 1) {
                            $_nav .= "    $_close_list\n    </li>\n";
                            $_depth--;
                        }
                        
                        $_link = str_replace('_', ' ', substr($link, 1));
                        $link = substr($link, 1);

                        if ($_link_depth < 2) continue;
                        if ($is_single === false)
                            $_page_link = "    <li><a href=\"$_page_name.html#_" . str_replace('-', '_', $link) . "\">$_link</a></li>\n";
                        else
                            $_page_link = "    <li><a href=\"#_" . str_replace('-', '_', $link) . "\">$_link</a></li>\n";
                        $_nav .= $_page_link;
                    }
                } else {
                    if ($_depth == 2)
                        $_nav .= "     $_close_list\n    </li>\n   $_close_list\n   </li>\n";
                    else if ($_depth == 1)
                        $_nav .= "    $_close_list\n   </li>\n";

                    $_depth = 0;

                    $_link = str_replace('_', ' ', $link);
                    $link = str_replace('_', '-', $link);
                
                    if ($_index == 0) {
                        $_nav_link = "   <li><a href=\"#\">$_link</a></li>";
                        $_page_link = "   <li><a href=\"#" . str_replace(' ', '_', $_link) . "\">$_link</a></li>";
                    } else {
                        $_nav_link = "   <li><a href=\"{$link}.html\">$_link</a></li>\n";
                        $_page_link = "   <li><a href=\"#" . str_replace(' ', '_', $_link) . "\">$_link</a></li>";
                    }

                    if ($is_single === false)
                        $_nav .= $_nav_link;
                    else
                        $_nav .= $_page_link;
                }
            }
        }

        if ($_depth == 2) $_nav .= "    $_close_list\n   </li>\n   $_close_list\n   </li>\n";
        else if ($_depth == 1) $_nav .= "   $_close_list\n   </li>\n";

        return $_nav . "   $_close_list\n";;
    }


//  Builds a multi-page document - a bit misleading
//  since it can also build a single if we set
//  $is_single to true - this was another hack
//  job.
//
//  I created a workaround to hijack this method
//  for creating single pages - needs refactoring.
//
private function _buildMultiPage($is_single = false)
    {
        $_links = $this->_links;
        $_pages = $this->_pages;

        for ($_index = 0; $_index < count($_pages); $_index++) {
            if ($is_single === false || ($is_single === true && $_index < 1)) {
                $_document = $this->_getPageOpen(
                    $_index,
                    $_links,
                    $_pages
                );
            } else $_document = "";

            $_document .= $this->_getPageNav(
                $_index,
                $_links,
                $_pages,
                TEMPLATE_KEY['SECTION_TOP'],
                $is_single
            );

            $_document .= $this->_getNavLinks(
                $_index,
                $_links,
                $_pages,
                $is_single
            );

            $_document .= $_pages[$_index];

            $_document .= $this->_getPageNav(
                $_index,
                $_links,
                $_pages,
                TEMPLATE_KEY['SECTION_BOTTOM'],
                $is_single
            );
        
            if ($is_single === false || ($is_single === true && $_index == (count($_pages) -1))) {
                $_document .= $this->_getPageClose(
                    $_index,
                    $_links,
                    $_pages
                );
            }
            array_push($this->_document, $_document);
        }

        $this->_dump();

        return true;
    }


//  Another lie! Se how I'm using _buildMultiPage()
//  to get the HTML?
//
//  Aye - sue me!
//
private function _buildSinglePage()
    {
        if ($this->_silent === false)
            echo "Building single page document\n\n";
    
        $_document = "";

        $this->_buildMultiPage(true);

        foreach ($this->_document as $document)
            $_document .= $document . "\n";

        $this->_document = Array();
        array_push($this->_document, $_document);

        return true;
    }


//  The _createMultiPage() method simply compiles
//  one or more $this->_documents, these are HTML
//  documents that are ripe for writing.
//
//  This function cycles through the list of
//  prepared _document's and creates them.
//
//  Unless it's a single-page document in which
//  case there will be only 1 _document in the
//  array.
//
private function _createDocuments()
    {
        if ($this->_silent === false)
            echo "Creating " . count($this->_document) . " HTML documents\n";
        
        $_output_path = $this->_output_path;
        $_documents = $this->_document;

        foreach ($_documents as $index=>$document) {
            $_pathinfo = pathinfo($this->_file_name[$index]);
            $_file_name = $_pathinfo['filename'];

            $_document_path = __buildpath(Array($_output_path, $_file_name . ".html"));

            file_put_contents($_document_path, $document);
        }

        return true;
    }


//  Build the HTML documents using the current
//  configuration.
//
private function _build()
    {
        chdir(PATH_CURRENT);

        $_output_path = $this->_output_path;

        if (! is_dir($_output_path)) {
            if ($this->_silent === false)
                echo "Creating output directory $_output_path\n";
            mkdir($_output_path, 0775);
        } else {
            if ($this->_silent === false)
                echo "Output directory $_output_path exists\n";
        }

        $_css_path = __buildpath(Array($_output_path, "css"));
        
        if (! is_dir($_css_path)) {
            if ($this->_silent === false)
                echo "Creating CSS directory $_css_path\n";
            mkdir($_css_path, 0775);
        } else {
            if ($this->_silent === false)
                echo "CSS directory $_css_path exists\n";
        }

        $_css_path = __buildpath(Array($_output_path, "css", "main.css"));

        if ($this->_silent === false) echo "Creating CSS file $_css_path\n";
        file_put_contents($_css_path, $this->_css_template);

        if ($this->_silent === false) echo "\n";

    //  First compile the _document's
    //
        if ($this->_multi_page)
            $this->_buildMultiPage();
        else
            $this->_buildSinglePage();

    //  Now create the HTML from the _document's
    //
        return $this->_createDocuments();
    }


private function _dump()
    {
        $_documents = $this->_document;
        if ($this->_silent === false) {
            echo "Creating " . count($_documents) . " HTML pages in " . $this->_output_path . ":\n\n";
            
            foreach ($_documents as $index=>$document) {
                $_pathinfo = pathinfo($this->_file_name[$index]);
                $_file_name = $_pathinfo['filename'];
                $_path = __buildpath(Array($this->_output_path, $_file_name . ""));
                echo " $_path (" . strlen($document) . " bytes)\n";
            }

            echo "\n";
        }
    }

}