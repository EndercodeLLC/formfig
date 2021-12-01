# formfig
## Minimalist CMS and MVC framework for PHP and MySQL/MariaDB

A CMS generator based on .toml config files that define your:

- CMS Forms (and front-end ones that can be short-coded into Views)
- Migrations (that include relationship info)
- Routes

You create custom scripts for:

- Models
- Views
- Controllers

There are custom functions for:

- DB functions
- Output status & messages
- Publishing

The goal of this CMS is to:

- Streamline form creation in CMS
- Use Migrations/Routes/MVC without needing Laravel or other complex framework.
- Give some useful helper functions

Extended description:

This CMS generator and MVC Framework combines a few top ideas from modern MVC
frameworks, and gets rid of the less useful stuff, opting instead for the plain
PHP/MySQL beginner devs are used to writing.

Forms made easy. Migrations. Routes. MVC. Publishing. Utility functions.

Everything else is just the PHP/MySQL business logic you're used to.

Installs as a composer package. Or download from source.

A few terminal commands you'll get used to fast:
`formfig cms` (builds cms from form configs)
`formfig migrate`

Edit global.scss to easily customize CMS design.

Ideas:

Forms should have proper HTML5.
Front-end and back-end validation & filtering.
Strictly semantic HTML, so CSS templates can be available.
Installable as composer package via create-project: https://stackoverflow.com/questions/23883838/how-to-create-your-own-package-for-composer-create-project
I think that means making a repo "formfig/app" for main folder structure and editable files, but that repo itself should require "formfig/framework". Right now it's "EndercodeLLC/formfig". Maybe I can make another called "EndercodeLLC/formfig-framework". I guess if I want to decouple it from branding, I should do the former.

First steps:

I should probably start with migrations. No need for all the fakr stuff. Try to
replicate exactly what's available with mysql/mariadb. Relationships can be simply
defined by many-to-whatever.

Migrations files MUST only concern one table, other than references to other
tables it has relationships with. They must be named "<table_name>-y-m-d-h-m-s.toml.

"<table_name>.toml" is built with the "formfig cms" command, and includes all
up-to-date info on table structure as defined by previous migrations. If you were
to delete all your <table_name> migrations, and create a new one with the contents
of this file, it would simply work (well, it would delete and remake the table).

A very simple ORM can be made where you "find" or "all" from a table name, and
it automatically generates a sql select behind the scenes. If you do a "with",
it looks at relationship data and forms the proper join. Should have some
functions to allow changing what you select. Should output a nicely formatted
associative array.

Routes can be POST or GET. You can define rules for what parts of route are
name, and what parts are parameters. You can send those parameters to a
particular controller (default function), or a particular function in that
controller if you define it. Don't bother with fancy grouping.

Models should define functions you can use in ORM. 

Views should just expect PHP files that output something. Inline PHP.
No templating language.

Controllers should simply be classes you use for your business logic.

Utility class should have all db, messaging, and publishing helper functions.

Have this use a .env file, and make a .example.env.

---

The left-menu and breadcrumb trail are generated from folder structure.

cms/pages/<name>.toml
cms/pages/polls/basic_info.toml

A "page" corresponds to a page in the CMS. It can have one or more forms.

You can optionally specify that a page is "hidden" in left-menu.
You can define a page as the "default" page for a folder. Otherwise default is alphabetical order.

forms/<name>.toml
forms/<foldername>/<name>.toml

Forms can be defined inside the menu config, or reference a separate form config file.

There is only one submit button per form.

All forms are click-to-save. No immediate ajax updating. Unless you set form type to "immediate". They are all-or-nothing in that regard.

Pages can combine elements into one form. Pages can have more than one form. Pages can specify certain form elements be added / removed from an imported form.

There should be a sitenav.toml somewhere that defines parts of the CMS unique to every page. Think top-nav.