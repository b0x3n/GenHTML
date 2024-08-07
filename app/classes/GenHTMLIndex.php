<?php


    namespace app\classes;


/*---------------------------------------------------------
 |
 |  INDENT_CHARS
 |
 |  If a line in an input file begins with any of these
 |  characters it's considered an indent.
 |
 */
    __defifndef('INDENT_CHARS', Array(
        " ", "\t"
    ));


/*---------------------------------------------------------
 |
 |  PUNCT_CHARS
 |
 |  These typically end paragraphs.
 |
 */
    __defifndef('PUNCT_CHARS', Array(
        '.', ',', ';', ':', '!', '?'
    ));


/*---------------------------------------------------------
 |
 |  PAGE and SECTION identifiers - the input pages are
 |  broken into sections and sub-sections of the following
 |  types:
 |
 */
    __defifndef('PAGE_TOP',             'PAGE_TOP');
    __defifndef('PAGE_SECTION',         'PAGE_SECTION');
    __defifndef('PAGE_BOTTOM',          'PAGE_BOTTOM');

    __defifndef('SECTION_HEADER',       'SECTION_HEADER');
    __defifndef('SECTION_SUBHEADER',    'SECTION_SUBHEADER');
    __defifndef('SECTION_PARAGRAPH',    'SECTION_PARAGRAPH');
    __defifndef('SECTION_CODEBLOCK',    'SECTION_CODEBLOCK');


/*---------------------------------------------------------
 |
 |  GenHTMLIndex
 |
 |  Will load the main index file (see _loadIndex())
 |  and any other files that it indexes.
 |
 |  Loke other objects, we need only pass the required
 |  objects to the constructor. The constructor will
 |  then call _loadIndex() which will load, parse and
 |  sort the input files in preparation for the final
 |  build.
 |
 |  Will set the $_error_message in the parent controller
 |  if there are any issues.
 |
 */
Class GenHTMLIndex extends GenHTMLController
{

    public          $_options;
    public          $_templates;

    public          $_file_name;
    public          $_file_data;

    public          $_page;
    public          $_link;


public function __construct($options, $templates)
    {
        $this->_options = $options;
        $this->_templates = $templates;

        $this->_file_name = Array();
        $this->_file_data = Array();

        $this->_page = Array();
        $this->_link = Array();

    //  Call the _loadIndex() method, this loads and
    //  parses the main index which may include other
    //  input files.
    //
        if ($this->_loadIndex() === false)
            return;

        $this->_dump();
    }


//  Returns the $line indentation level.
//
private function _lineIndent($line)
    {
        $_char_no = 0;

        while ($_char_no < strlen($line)) {
            $_char_in = substr($line, $_char_no, 1);

            if (array_search($_char_in, INDENT_CHARS) === false)
                break;

            $_char_no++;
        }

        return $_char_no;
    }


//  Returns true if $line is empty or contains
//  a single newline character.
//
private function _emptyLine($line)
    {
        $_line_indent = $this->_lineIndent($line);
        $_line = trim(substr($line, $_line_indent));

        if (empty($_line) || $_line == "\n")
            return true;

        return false;
    }


//  Returns true if the given $section is a
//  header.
//
private function _isHeader($section, $lines, &$depth)
    {
        if ($lines != 1)
            return false;
        if (($_line_indent = $this->_lineIndent($section)) != 0)
            return false;
        
        $_section = rtrim($section);
        $_last_char = substr($_section, (strlen($_section) - 1), 1);

        if (array_search($_last_char, PUNCT_CHARS) !== false)
            return false;

        $depth = 0;

        return true;
    }


//  Returns true if the given section is a
//  subheader.
//
private function _isSubheader($section, $lines, &$depth)
    {
        if ($lines != 1)
            return false;
        
        $_line_indent = $this->_lineIndent($section);

        if ($_line_indent < 1 || $_line_indent > 2)
            return false;
        
        $_section = rtrim($section);
        $_last_char = substr($_section, (strlen($_section) - 1), 1);

        if (array_search($_last_char, PUNCT_CHARS) !== false)
            return false;

        $depth = $_line_indent;

        return true;
    }
   

//  Returns true if the given section is a
//  paragraph.
//
private function _isParagraph($section, $lines, &$depth)
    {
        if ($lines < 1)
            return false;

        $_section = rtrim($section);
        $_last_char = substr($_section, (strlen($_section) - 1), 1);

        if (array_search($_last_char, PUNCT_CHARS) === false)
            return false;

        $_line_indent = $this->_lineIndent($_section);

        if ($_line_indent == ($depth + 1))
            return true;

        if ($depth) {
            if ($_line_indent == ($depth - 1))
                return true;
        }

        return false;
    }

  
//  Returns true if the given section is a
//  codeblock.
//
private function _isCodeblock($section, $lines, &$depth)
    {
        if ($lines < 1)
            return false;

        $_section = rtrim($section);
        $_line_indent = $this->_lineIndent($_section);
        
        if ($_line_indent == ($depth + 3))
            return true;

        return false;
    }
    

//  Parses a section - expands SECTION directives
//  to generate the HTML for this section.
//
private function _parseSection(
        $file_index,
        $section, 
        $lines, 
        &$page, 
        &$depth
    )
    {
        if (! isset($this->_link[$file_index]))
            $this->_link[$file_index] = Array();
            
        if ($this->_isHeader($section, $lines, $depth)) {
            $page .= str_replace(
                '{{SECTION_HEADER}}',
                trim($section),
                $this->_templates->_html[SECTION_HEADER]
            );

            $_link = str_replace(' ', '_', $section);
            
            $page = str_replace(
                '{{SECTION_NAME}}',
                trim($_link),
                $page
            );

            array_push($this->_link[$file_index], $_link);

        //  The main header of the main index file
        //  becomes the site-wide title.
        //
            if ($file_index == 0)
                $this->_options->_title = trim($section);
        }

        else if ($this->_isSubheader($section, $lines, $depth)) {
            $page .= str_replace(
                '{{SECTION_SUBHEADER}}',
                trim($section),
                $this->_templates->_html[SECTION_SUBHEADER]
            );

            $_link = str_replace(' ', '_', $section);
            
            $page = str_replace(
                '{{SECTION_NAME}}',
                trim($_link),
                $page
            );

            array_push($this->_link[$file_index], $_link);
        }

        else if ($this->_isParagraph($section, $lines, $depth)) {
            $page .= str_replace(
                '{{SECTION_PARAGRAPH}}',
                trim($section),
                $this->_templates->_html[SECTION_PARAGRAPH]
            );
        }

        else if ($this->_isCodeblock($section, $lines, $depth)) {
            $page .= str_replace(
                '{{SECTION_CODEBLOCK}}',
                "\n" . rtrim($section),
                $this->_templates->_html[SECTION_CODEBLOCK]
            );
        }
        

        return true;
    }


//  As you'd expect - loads an input file
//  ripped from the main index.
//
private function _loadInputfile($file_name)
    {
        $_path = __buildpath(Array(
            $this->_options->_input_path,
            str_replace(' ', '-', $file_name)
        ));

        if (! is_file($_path))
            return parent::_setError("_loadInputFile(): Cannot load input file " . $_path);

        array_push($this->_file_name, $_path);
        array_push($this->_file_data, file_get_contents($_path));

        return true;
    }


//  Parse and sort the given input file data.
//
private function _processFile(
        $file_index,
        $file_name, 
        $file_data
    )
    {
    //  Parse the input into an array of lines and
    //  maintain current line count.
    //
        $_lines = preg_split('/\\n/', $file_data, null, PREG_SPLIT_DELIM_CAPTURE);
        $_line = 0;

    //  Maintain current indentation depth.
    //
        $_depth = 0;

    //  Collect section data and maintain the number
    //  of lines added to the section.
    //
        $_section = false;
        $_section_lines = 0;

    //  Set to true when $_section is a SECTION_CODEBLOCK
    //
        $_is_codeblock = false;

    //  The returned output page is collected here.
    //
        $_page = "";

        while ($_line < count($_lines)) {
            $_line_in = $_lines[$_line++];
            $_line_indent = $this->_lineIndent($_line_in);

            $_line_empty = $this->_emptyLine($_line_in);

            if ($_line_empty === false && $_line_indent == ($_depth + 2)) {
                if ($this->_loadInputFile(trim($_line_in)) === false)
                    return false;
            }

        //  Break and reset the section.
        //
            if (
                ($_is_codeblock === false && $_line_empty) ||
                ($_line_empty === false && $_is_codeblock === true && $_line_indent < ($_depth + 3))
            ) {
                if ($this->_parseSection(
                    $file_index,
                    $_section, 
                    $_section_lines,
                    $_page,
                    $_depth
                ) === false)
                    return false;

                $_section = false;
                $_section_lines = 0;
                $_is_codeblock = false;

                if ($_line_empty === false)
                    $_line--;

                continue;
            }

            if ($_section == false) {
                $_section = rtrim($_line_in);
                if ($_line_indent == ($_depth + 3))
                    $_is_codeblock = true;
            }
            else {
                if ($_is_codeblock)
                    $_section .= "\n" . $_line_in;
                else
                    $_section .= " " . trim($_line_in);
            }

            $_section_lines++;
        }

        $_page_section = str_replace(
            '{{PAGE_SECTION}}',
            $_page,
            $this->_templates->_html[PAGE_SECTION]
        );

        array_push($this->_page, $_page_section);

        return true;
    }


//  Loop processed the array of input files.
//
private function _processFiles()
    {
        for ($_index = 0; $_index < count($this->_file_name); $_index++) {
            if ($this->_processFile(
                    $_index,
                    $this->_file_name[$_index],
                    $this->_file_data[$_index]
            ) === false)
                return false;
        }

        return true;
    }


//  Loads and parses the main index file, then
//  calls the _processFiles() method.
//
private function _loadIndex()
    {
        $_input_path = $this->_options->_input_path;
        $_index_path = $this->_options->_index_path;

        $_path = __buildpath(Array($_input_path, $_index_path));

        if (! is_file($_path))
            return parent::_setError("_loadIndex(): Cannot find main index file " . $_path);

        $_index_data = file_get_contents($_path);

        array_push($this->_file_name, $_index_path);
        array_push($this->_file_data, $_index_data);

        return $this->_processFiles();
    }


private function _dump()
    {
        if ($this->_options->_silent === true)
            return;

        $_pages = $this->_page;

        echo "Dumping a list of " . count($_pages) . " pages:\n\n";

        foreach ($_pages as $index=>$page)
            echo " Page $index ({$this->_file_name[$index]} (" . strlen($page) . " bytes)\n";
        echo "\n";

        $_links = $this->_link;

        foreach ($_links as $index=>$links) {
            echo " Linking " . count($links) . " sections in file " . $this->_file_name[$index] . "\n\n";

            $_link = $links;

            foreach ($_link as $index=>$link)
                echo "  $link\n";
            echo "\n";
        }
    }

}

