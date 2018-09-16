[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fpark-manager%2Fpark-manager.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fpark-manager%2Fpark-manager?ref=badge_shield)

Park-Manager
============

Park-Manager is a full-stack **hosting-management system**.

Park-Manager is provided as [Free Software][1] and can be completely customized
and extended to your needs.

 ##### :warning: Park-Manager is still actively developed. The current status is pre-alpha and not ready for production usage.
 ##### Major code changes should be expected.

Architecture
------------

The Park-Manager architecture consists of:

* The **Park-Manager framework** that supplies a set of reusable **PHP components**.
* The **Park-Manager platform** that provides the basics for any **hosting system**.
* The Park-Manager modules that provide the actual functionality, including:
  * Webhosting
  * DNS management
  * Support tickets

**Module system:**

> The Park-Manager module system was highly inspired on the [Symfony's bundle architecture][2]. 
> In fact, **a module is a bundle**, with some added functionality.

Installation
------------

* Install the Park-Manager system with [Docker][3] (*currently not ready*).
* Install the Park-Manager framework libraries (for your own projects) with [Composer][4].
* Park-Manager follows the [semantic versioning][5] strictly, 
  and has a release process that is predictable and business-friendly.

Contributing
------------

Park-Manager is an Open Source, community-driven project of contributors. 
Join them with [contributing code][6] or [contributing documentation][7].

Code of Conduct
---------------

To ensure a community that is welcoming to all, please review and abide 
by the [Code of Conduct][8].

License
-------

The Park-Manager framework libraries are released under the [MIT license](LICENSE.MIT).

The code of the Park-Manager platform and modules are subject to the 
terms of the Mozilla Public License, version 2.0 (MPLv2.0).

**Note:**

> Unlike the MIT license, the **MPLv2.0 license does not allow (re)distribution
> of a closed-source "only" version** of the software. 
>
> See the [MPLv2.0 License](LICENSE) for all legal details.

About us
--------

Park-Manager is led by [Sebastiaan Stok (@sstok)][9] and supported by
the Park-Manager [contributors][10].

[1]: https://www.gnu.org/philosophy/free-sw.html
[2]: http://symfony.com/doc/current/bundles.html
[3]: https://www.docker.com/
[4]: https://getcomposer.org/
[5]: http://semver.org/
[6]: https://docs.park-manager.com/current/contributing/code/index.html
[7]: https://docs.park-manager.com/current/contributing/documentation/index.html
[8]: https://github.com/park-manager/park-manager/blob/master/CODE_OF_CONDUCT.md
[9]: https://github.com/sstok
[10]: https://github.com/park-manager/park-manager/blob/master/AUTHORS


## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fpark-manager%2Fpark-manager.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fpark-manager%2Fpark-manager?ref=badge_large)