# GenHTML v1

A command line utility that allows you to create
__HTML__ documents from __raw text__ files.


## Why?

Personal reasons! When I build an application I
tend to document it in two formats, __text__ and
__HTML__.

What I normally did, was first write the text
documentation, then take a copy of that and
mark it up with HTML.

But I wanted to do away with that, the markup
and formatting. I wanted an application that
could take my raw but presentable and readable
text documentation and build the HTML directly
from  that, without any extensive markup - in
simple terms I want to write my docs one single
time, in a neat and presentable text format then
run a single command to get the HTML equivalent.

Laziness, really. I was motivated by laziness,
odd but true.

So I created __GenHTML__, it's fairly simple - it
uses indentation to break the input files into
pages and sections, and links the pages and
sections together.

This is version 1, written in php. To be honest
I mostly hacked it together out of necessity, too
much time was being wasted writing and marking up
documentation.

On the next release I might re-build in c or
another language, but for now it's doing everything
I need it to - it doesn't output anything too
fancy, it's just documentation at the end of the
day - but it's configurable and quick and that's
the point.


## Example

I used __GenHTML__ to build the HTML documentation
for __GenHTML__!!

See the text docs in:

```GenHTML/doc/txt/```

And the HTML documents in:

```GenHHTML/doc/HTML/```

__GenHTML__ was used to turn those text files into
the HTML files. The text files were processed as
they appear, now - no additional formatting or
markup was required.

The documentation describes the __GenHTML__
application in some detail, so if you want to 
try it out then that's a good place to start.


## To do

Some of the classes need re-factoring and there is
additional functionality I'd like to add - I'm
torn between modifying this release or rebuilding
in c.

For now, though - I have other projects to work
on so __GenHTML__ is something I'll have to come
back to in future.


## Issue

I noticed that when generating a single-page document
that for some reason the horizontal rule and page
break isn't being inserted. this worked at one point
during production - links to skip between sections were
inserted between each page...but not any more! I am a
bad boy! S....s'embarassinnnnnnn!

Why didn't I notice this? Most of the testing I
did was for compiling multi-page as opposed to
single-page documents!

I reckon some changes to the templating system will
also be amarked improvement - when I finish __bitraq__
I'll re-do this as I found this utility to be quite
useful.

I'll look into this at some point, just wanted to
leave a note here lest I forget!

