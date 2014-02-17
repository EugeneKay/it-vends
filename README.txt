#
# README.txt
# it-vends
#

    +---------------------------------------------------------------------+
    |   __________________           _______ _       ______  _______ _    |
    |   \__   __/\__   __/  |\     /|  ____ \ (    /|  __  \(  ____ \ )   |
    |      ) (      ) (     | )   ( | (    \/  \  ( | (  \  ) (    \/ |   |
    |      | |      | |     | |   | | (__   |   \ | | |   ) | (_____| |   |
    |      | |      | |     ( (   ) )  __)  | (\ \) | |   | |_____  ) |   |
    |      | |      | |      \ \_/ /| (     | | \   | |   ) |     ) |_)   |
    |   ___) (___   | |       \   / | (____/\ )  \  | (__/  )\____) |_    |
    |   \_______/   )_(        \_/  (_______//    )_)______/\_______)_)   |
    |                                                                     |
    +---------------------------------------------------------------------+


Table of Contents
	1) About
	2) Copyright
	3) Acknowledgements
	4) Changelog
	5) License
	6) Installation
	7) Contributions


1) About

It Vends is a small, silly project to distribute items at random to the internet
public. It also provides a basic API(via vend.php) to request items for usage by
automated IRC bots or similar. A full RESTful API is in the testing phase, with
support planned for social network integration.


2) Copyright

It Vends is Copyright 2011 by the persons listed in the Authors section of this
README document. Detailed authorship can be found by examing the source code
repository via the "git-blame" command.

The "It Vends" name is Copyright 2010 Eugene E. Kashpureff, and is pending
trademark registration in The United States of America. Usage of this term
(including the phrase "It Vends!") is permitted within reason and when used in a
manner consistent with the image of the brand. See the License section below for
further information.

Included libraries(jQuery, Sizzle, jQuery UI, jQuery doTimeout) are copyright by
their respective authors, as noted in their source files.


3) Authors

Maintainer:
*Eugene E. Kashpureff Jr (eugene@kashpureff.org)

Primary Authors:
*Eugene E. Kashpureff Jr (eugene@kashpureff.org)
*Jeffrey C. "Entomo" Hoyt (jchoyt@gmail.com)
*William Cahill-Manley(william@cahillmanley.com)

Contributors:
*sannse
*Kyte
*ShadowShadok


4) Changelog

Changelog for this release:

v1.3.0

*Improve redirect behaviour
*Update to newer jQuery
*Multiple typofixes & additional items
*Higher ITEMLIMIT for butt scale
*Benchmarking support
*Add "bag" page for container support.

See the CHANGELOG file for historical entries, or the git source repository for
per-commit change information.


5) License

It Vends is offered as free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the Free
Software Foundation, either version 3 of the License, or (at your option) any
later version. If you wish to obtain It Vends under another license(including a
proprietary commercial license) for inclusion into other software or use in a
situation where the GNU GPL is not acceptable, you may contact the Maintainer
for further information.

When obtained under the GNU General Public License, It Vends is distributed in
the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details. You should have received a copy of
the GNU General Public License along with It Vends. If not, you may obtain it at
(http://www.gnu.org/licenses/)

Usage of the 'It Vends' mark is restricted to the primary It Vends website at
(http://itvends.com), references to the original site, and references to the
It Vends software which is distributed at (http://github.com/eugenekay/it-vends)
Further permission is given to use this mark in a manner consistent with the
purpose and image of the brand, eg, within the phrase "It Vends!".

Any public distribution, fork or installation of It Vends which differs
substantially from the original must be rebranded to prevent confusion with the
canonical installation located at (http://itvends.com) and the associated 'It
Vends' mark. Reference to It Vends is permitted(eg, "Based upon It Vends"),
within the same 'reasonable' conditions of the mark. Operation of a mirror site,
containing only a faithful recreation of the original, is permitted, with the
provision that it is to be marked as such.

Software or services which include It Vends code must maintain the Authors
listing and License information contained in this document within their source
code and any further redistributions. You may change the License to any license
which is forward-compatible with the License under which you obtained It Vends,
including any versions of the GNU General Public License higher than 3, or the
GNU Affero General Public License version 3 or higher.

Software or services which utilize It Vends in an unchanged or minimally altered
format(eg, dynamic or static linking of the code), or via the service provided
at (http://itvends.com) may do so without further attribution, but any such
attribution is appreciated by the authors, if present. The suggested format is:
"Powered by It Vends (http://itvends.com)", with the 'It Vends' mark being a
hyperlink to the named website, if the medium supports it.

The full source code for It Vends is available at
https://github.com/eugenekay/it-vends

It Vends includes the jQuery library, and it is distributed under the terms of
the GNU General Public License, version 3, in accordance with the allowances of
the MIT "Expat" License(included with It Vends as licenses/jQuery-MIT.txt). You
may obtain a copy of the jQuery library for your own use at (http://jquery.com/)

It Vends includes the Sizzle.js library, and it is distributed under the terms
of the GNU General Public License, version 3, in accordance with the allowances
of the MIT "Expat" License(included with It Vends as licenses/Sizzle-MIT.txt).
You may obtain a copy of the Sizzle.js library for your own use at
(http://sizzlejs.com/)

It Vends includes the jQuery User Interface library, and it is distributed under
the terms of the GNU General Public License, version 3, in accordance with the
allowances of the MIT "Expat" License(included with It Vends as
licenses/jQuery-ui-MIT.txt). You may obtain a copy of the jQuery User Interface
library for your own use at (http://jqueryui.com/)

It Vends includes the jQuery doTimeout plugin, and it is distributed under the
terms of the GNU General Public License, version 3, in accordance with the
allowances of the MIT "Expat" License(included with It Vends as
licenses/jquery-dotimeout-MIT.txt). You may obtain a copy of jQuery doTimeout
for oyur own use at (http://benalman.com/projects/jquery-dotimeout-plugin/)


6) Installation

It Vends may be installed in the root directory of any VirtualHost. The current
.htaccess assumes usage of the itvends.com domain and an Apache web server. Your
setup will inevitably differ, and the configuration should be changed to fit.


7) Contributions

As stated in Section 5, It Vends is chiefly distributed under the terms of the
GNU General Public License, version 3(or later, at your option) without warranty
or guarantee. However, the primary authors of It Vends reserve the right to
relicense It Vends under any suitable license, including(but not limited to) a
proprietary commercial license. In order to maintain this right, any
contributions to It Vends must be made under a Contributor License Agreement in
order to be accepted.
