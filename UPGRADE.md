# UPGRADING
## Recommended way of upgrading
Since these are 3 possible scenarios how you can use shopsys, instructions are divided by these scenarios.

### You use our packages only
Follow instructions in relevant sections, eg. `shopsys/coding-standards`, `shopsys/microservice-product-search`.

### You are using monorepo
Follow instructions in the section `shopsys/shopsys`.

### You are developing a project based on project-base
* upgrade only your composer dependencies and follow instructions
* if you want update your project with the changes from [shopsys/project-base], you can follow the *(optional)* instructions or cherry-pick from the repository whatever is relevant for you but we do not recommend rebasing or merging everything because the changes might not be compatible with your project as it probably evolves in time
* check all instructions in all sections, any of them could be relevant for you
* upgrade locally first. After you fix all issues caused by the upgrade, commit your changes and then continue with upgrading application on a server
* upgrade one version at a time:
    * Start with a working application
    * Upgrade to the next version
    * Fix all issues
    * Repeat
* typical upgrade sequence should be:
    * `docker-compose down`
    * follow upgrade notes for `docker-compose.yml`, `Dockerfile`, docker containers
    * `docker-compose up -d`
    * update shopsys framework dependencies in `composer.json` to version you are upgrading to
        eg. `"shopsys/framework": "v7.0.0-beta1"`
    * `composer update`
    * follow all upgrade notes you have not done yet
    * `php phing clean`
    * `php phing db-migrations`
    * commit your changes
* even we care a lot about these instructions, it is possible we miss something. In case something doesn't work after the upgrade, you'll find more information in the [CHANGELOG](CHANGELOG.md)

There is a list of all the repositories maintained by monorepo, changes in log below are ordered as this list:

* [shopsys/framework]
* [shopsys/project-base]
* [shopsys/shopsys]
* [shopsys/coding-standards]
* [shopsys/form-types-bundle]
* [shopsys/http-smoke-testing]
* [shopsys/migrations]
* [shopsys/monorepo-tools]
* [shopsys/plugin-interface]
* [shopsys/product-feed-google]
* [shopsys/product-feed-heureka]
* [shopsys/product-feed-heureka-delivery]
* [shopsys/product-feed-zbozi]
* [shopsys/microservice-product-search]
* [shopsys/microservice-product-search-export]

## [From 7.0.0-beta1 to Unreleased]
### [shopsys/project-base]
- [#497 adding php.ini to image is now done only in dockerfiles](https://github.com/shopsys/shopsys/pull/497)
    - you should make the same changes in your repository for the php.ini configuration files to be added to your Docker images
    - from now on, you will have to rebuild your Docker images (`docker-compose up -d --build`) for the changes in the php.ini file to apply
- [#494 Microservices webserver using nginx + php-fpm](https://github.com/shopsys/shopsys/pull/494)
    - execute `docker-compose pull` to pull new microservice images and `docker-compose up -d` to start newly pulled microservices
    - url addresses to microservices have changed, you need to upgrade url address provided in `app/config/parameters.yml`  
        - update parameter `microservice_product_search_url` from `microservice-product-search:8000` to `microservice-product-search`
        - update parameter `microservice_product_search_export_url`, from `microservice-product-search-export:8000` to `microservice-product-search-export`
- [#502 - fixed acceptance tests (loading DB dump)](https://github.com/shopsys/shopsys/pull/502)
    - when you upgrade `codeception/codeception` to version `2.5.0`, you have to change parameter `populate` to `true`
      in `tests/ShopBundle/Acceptance/acceptance.suite.yml`
- make changes in `composer.json`:
    - remove repositories:
        - `https://github.com/shopsys/doctrine2.git`
        - `https://github.com/shopsys/jparser.git`
        - `https://github.com/molaux/PostgreSearchBundle.git`
    - remove dependencies:
        - `"timwhitlock/jparser": "@dev"`
    - change dependencies:
        - `"doctrine/orm": "dev-doctrine-260-..."` -> `"shopsys/doctrine-orm": "2.6.2"`
        - `"intaro/postgres-search-bundle": "@dev"` -> `"shopsys/postgres-search-bundle": "0.1"`

## [From 7.0.0-alpha6 to 7.0.0-beta1]
### [shopsys/framework]
- *(optional)* [#468 - Setting for docker on mac are now more optimized](https://github.com/shopsys/shopsys/pull/468)
    - if you use the Shopsys Framework with docker on the platform Mac, modify your
      [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-compose-mac.yml.dist)
      and [`docker-sync.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-sync.yml.dist) according to the new templates
    - next restart docker-compose and docker-sync
- *(optional)* [#483 - updated info about Docker on Mac](https://github.com/shopsys/shopsys/pull/483)
    - if you use Docker for Mac and experience issues with `composer install` resulting in `Killed` status, try increasing the allowed memory
    - we recommend to set 2 GB RAM, 1 CPU and 2 GB Swap in `Docker -> Preferences… -> Advanced`
- we changed visibility of Controllers' and Factories' methods and properties to protected
    - you have to change visibility of overriden methods and properties to protected
    - you can use parents' methods and properties
- update `paths.yml`:
    - add `shopsys.data_fixtures_images.resources_dir: '%shopsys.data_fixtures.resources_dir%/images/'`
    - remove
      ```
        shopsys.demo_images_archive_url: https://images.shopsysdemo.com/demoImages.v11.zip
        shopsys.demo_images_sql_url: https://images.shopsysdemo.com/demoImagesSql.v8.sql
      ```
- remove phing target `img-demo` as demonstration images are part of data fixtures
    - remove `img-demo` phing target from `build.xml`
    - remove all occurrences of `img-demo` in `build-dev.xml`
    - remove all occurrences of `img-demo` from your build/deploy process

## [From 7.0.0-alpha5 to 7.0.0-alpha6]
### [shopsys/framework]
- check for usages of `TransportEditFormType` - it was removed and all it's attributes were moved to `TransportFormType` so use this form instead
- check for usages of `PaymentEditFormType` - it was removed and all it's attributes were moved to `PaymentFormType` so use this form instead
- check for usages of `ProductEditFormType` - it was removed and all it's attributes were moved to `ProductFormType` so use this form instead
- pay attention to javascripts bound to your forms as well as the elements' [names and ids has changed #428](https://github.com/shopsys/shopsys/pull/428)
    - e.g. change id from `#product_edit_form_productData` to `#product_form`
    - check also your tests, you need to change names and ids of elements too
- PHP-FPM and microservice containers now expect a GitHub OAuth token set via a build argument, so it is not necessary to provide it every time those containers are rebuilt
    - see the `github_oauth_token` argument setting in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf/docker-compose.yml.dist#L33) template you used and replicate it in your `docker-compose.yml`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - replace the `place-your-token-here` string by the token generated on [Github -> Settings -> Developer Settings -> Personal access tokens](https://github.com/settings/tokens/new?scopes=repo&description=Composer+API+token)
- as there were changes in the Dockerfiles, replace `php-fpm` dockerfile by a new version:
    - copy [`docker/php-fpm/Dockerfile`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/php-fpm/Dockerfile) from github
    - rebuild images `docker-compose up -d --build`
    - if you are in monorepo with microservices, just run `docker-compose up -d --build`
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - this change can affect your extended forms and entities, reflect this change into your project

### [shopsys/project-base]
- [Microservice Product Search Export](https://github.com/shopsys/microservice-product-search-export) was added and it needs to be installed and run
    - check changes in the `docker-compose.yml` template you used and replicate them, there is a new container `microservice-product-search-export`
    - `parameters.yml.dist` contains new parameter `microservice_product_search_export_url`
        - add `microservice_product_search_export_url: 'http://microservice-product-search-export:8000'` into your `parameters.yml.dist`
        - execute `composer install` *(it will copy parameter into `parameters.yml`)*
- *(optional)* instead of building the Docker images of the microservices yourself, you can use pre-built images on Docker Hub (see the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) template you used)
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - edit `ShopBundle/Form/Front/Customer/BillingAddressFormType` - remove `telephone`
    - edit `ShopBundle/Form/Front/Customer/UserFormType` - add `telephone`
    - edit twig templates and tests in such a way as to reflect the movement of `telephone` attribute according to the [pull request](https://github.com/shopsys/shopsys/pull/438)
- *(optional)* to use custom postgres configuration check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) templates and replicate them, there is a new volume for `postgres` container
    - PR [Improve Postgres configuration to improve performance](https://github.com/shopsys/shopsys/pull/444)
    - Stop running containers `docker-compose down`
    - Move data from `project-base/var/postgres-data` into `project-base/var/postgres-data/pgdata`. The directory must have correct permission depending on your OS.
      To provide you with a better image of what exactly needs to be done, there are instructions for Ubuntu:
        - `sudo su`
        - `cd project-base/var/postgres-data/`
        - trick to create directory `pgdata` with correct permissions
            - `cp -rp base/ pgdata`
            - `rm -fr pgdata/*`
        - `shopt -s extglob dotglob`
        - `mv !(pgdata) pgdata`
        - `shopt -u dotglob`
        - `exit`
    - Start containers `docker-compose up -d`
- *(optional)* configuration files (`config.yml`, `config_dev.yml`, `config_test.yml`, `security.yml` and `wysiwyg.yml`) has been split into packages config files, for details [see #449](https://github.com/shopsys/shopsys/pull/449)
    - extract each section into own config file
        - eg. from `config.yml` extract `doctrine:` section into file `packages/doctrine.yml`
        - eg. from `config_dev.yml` extract `assetic:` section info file `packages/dev/assetic.yml`
        - and also split `wysiwyg.yml` into `packages/*.yml`
            - *(since `config.yml` will include all files in `packages/`, splitted `wysiwyg.yml` will be included automatically)*
    - move `security.yml` to `packages/security.yml`
    - the only thing that have to be left in the original configuration files is the import of these new configuration files
        - eg. `config_dev.yml` will contain only
            ```
            imports:
                 - { resource: packages/dev/*.yml }
            ```
- phing targets and console commands for working with elasticsearch were renamed, so rename them in `build.xml`, `build-dev.xml`. Also if you call them from other places, rename calling too:
    - phing targets:
        - `elasticsearch-indexes-create` -> `microservice-product-search-create-structure`
        - `elasticsearch-indexes-delete` -> `microservice-product-search-delete-structure`
        - `elasticsearch-indexes-recreate` -> `microservice-product-search-recreate-structure`
        - `elasticsearch-products-export` -> `microservice-product-search-export-products`
    - console commands:
        - `shopsys:elasticsearch:create-indexes` -> `shopsys:microservice:product-search:create-structure`
        - `shopsys:elasticsearch:delete-indexes` -> `shopsys:microservice:product-search:delete-structure`
        - `shopsys:elasticsearch:export-products` -> `shopsys:microservice:product-search:export-products`
- run `php phing ecs-fix` to apply new coding standards - [keep class spacing consistent #384](https://github.com/shopsys/shopsys/pull/384)

### [shopsys/shopsys]
- when upgrading your installed [monorepo](docs/introduction/monorepo.md), you'll have to change the build context for the images of the microservices in `docker-compose.yml`
    - `build.context` should be the root of the microservice (eg. `microservices/product-search-export`)
    - `build.dockerfile` should be `docker/Dockerfile`
    - execute `docker-compose up -d --build`, microservices should be up and running

## [From 7.0.0-alpha4 to 7.0.0-alpha5]

### [shopsys/framework]
- for [product search via Elasticsearch](/docs/introduction/product-search-via-elasticsearch.md), you'll have to:
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container with Elasticsearch
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - since the fully installed and ready [Microservice Product Search](https://github.com/shopsys/microservice-product-search) is a necessary condition for the Shopsys Framework to run, the installation procedure of this microservice is a part of Shopsys Framework [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
        - alternately you can use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - run `docker-compose up -d`
    - update composer dependencies `composer update`
    - create Elasticsearch indexes by running `php phing elasticsearch-indexes-create`
    - export products into Elasticsearch by `php phing elasticsearch-products-export`
- `ProductFormType` [is extensible now #375](https://github.com/shopsys/shopsys/pull/375). If you extended the product form, you have to:
    - move form parts into right subsections, eg. [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
    - if you don't have custom rendering, remove your template for form
    - if you have custom rendering, change rendering of these parts as they are now in subsections
    - as the form changed structure, you have to also fix tests. see [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
        - form fields changed names and also ids

#### PostgreSQL upgrade:
We decided to move onto a newer version of PostgreSQL.

These steps are for migrating your data onto newer version of postgres and are inspired by [official documentation](https://www.postgresql.org/docs/10/static/upgrading.html):

If you are running your project natively then just follow [official instructions](https://www.postgresql.org/docs/10/static/upgrading.html), 
if you are using docker infrastructure you can follow steps written below.

1. create a backup of your database by executing::

    `docker exec -it shopsys-framework-postgres pg_dumpall > backupfile`

1. apply changes in `docker-compose.yml`, you can find them in a new version of [`docker-compose.yml.dist`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) templates

    *Note: select correct `docker-compose` according to your operating system*

    *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*

1. update version of `database_server_version` from *9.5* to *10.5* in your `parameters.yml`

1. stop containers and delete old data:

    `docker-compose down`

    `rm -rf <project-root-path>/var/postgres-data/*`

1. use a new version of `php-fpm` container:

    `curl -L https://github.com/shopsys/shopsys/raw/v7.0.0-alpha5/project-base/docker/php-fpm/Dockerfile --output docker/php-fpm/Dockerfile`

    `docker-compose build php-fpm`

1. start new docker-compose stack with newer version of postgres by just recreating your containers:

    `docker-compose up -d --force-recreate`

1. copy backup into postgres container root folder

    `docker cp backupfile shopsys-framework-postgres:/`

1. restore you data:

    `docker exec -it shopsys-framework-postgres psql -d postgres -f backupfile`

1. delete backup file:

    `docker exec -it shopsys-framework-postgres rm backupfile`

1. recreate collations:

    `docker exec shopsys-framework-php-fpm ./phing db-create test-db-create`

### [shopsys/project-base] 
- added [Microservice Product Search](https://github.com/shopsys/microservice-product-search)
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container `microservice-product-search`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
        - follow [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md) to install microservice
          or use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - into `parameters.yml.dist` add a new parameter `microservice_product_search_url`:
        - `microservice_product_search_url: 'http://microservice-product-search:8000'`
        - and add it also into `parameters.yml`
    - modify a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ProductSearchRepository`
        - `shopsys.microservice_client.product_search`
    - remove a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ElasticsearchSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\CachedSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\SearchClient`
- *(optional)* standardize indentation in your yaml files
    - you can find yaml files with wrong indentation with regexp `^( {4})* {1,3}[^ ]`
- *(optional)* we added a new phing target that checks [availabitliy of microservices](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/project-base/build-dev.xml#L726-L731).
  Feel free to include this target into your build process.
- add new themes to configuration `app/config/config.yml`, path `twig.form_themes`:
    ```
        - '@ShopsysFramework/Admin/Form/warningMessage.html.twig'
        - '@ShopsysFramework/Admin/Form/displayOnlyUrl.html.twig'
        - '@ShopsysFramework/Admin/Form/localizedFullWidth.html.twig'
        - '@ShopsysFramework/Admin/Form/productParameterValue.html.twig'
        - '@ShopsysFramework/Admin/Form/productCalculatedPrices.html.twig'
    ```
        
## [From 7.0.0-alpha3 to 7.0.0-alpha4]

### [shopsys/framework]
- move creation of data objects into factories
- already existing data object factories changed their signatures
- to change the last item in admin breadcrumb, use `BreadcrumbOverrider:overrideLastItem(string $label)` instead of `Breadcrumb::overrideLastItem(MenuItem $item)`
- if you've customized the admin menu by using your own `admin_menu.yml`, implement event listeners instead
    - see the [Adding a New Administration Page](/docs/cookbook/adding-a-new-administration-page.md) cookbook for details

### [shopsys/product-feed-google]
- move creation of data objects into factories
- already existing data object factories changed their signatures

### [shopsys/product-feed-heureka]
- move creation of data objects into factories
- already existing data object factories changed their signatures

### [shopsys/product-feed-zbozi]
- move creation of data objects into factories
- already existing data object factories changed their signatures

## [From 7.0.0-alpha2 to 7.0.0-alpha3]

### [shopsys/framework]
- classes in src/Components were revised, refactored and some of them were moved to model,
    for upgrading to newer version, you must go through commits done in [#272](https://github.com/shopsys/shopsys/pull/272) and reflect the changes of namespaces.
- FriendlyUrlToGenerateRepository: deleted. If you want to define your own data for friendly url generation, do it so by
    implementing the FriendlyUrlDataProviderInterface and tag your service with `shopsys.friendly_url_provider`.
- check changes in src/Model, all *editData*.php were merged into its *Data*.php relatives
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
- access multi-domain attributes of entities via their main entity (instead of the usual entity detail)
    - entity domains (eg. `BrandDomain`) should be created, edited and directly accessed only in their main entities (eg. `Brand`) 
    - see [#165 Different approach to multidomain entities](https://github.com/shopsys/shopsys/pull/165) for details
- `DomainsType` now uses array of booleans indexed by domain IDs instead of array of domain IDs, original behavior can be restored by adding model data transformer `IndexedBooleansToArrayOfIndexesTransformer`
- `CategoryDomain::$hidden` was changed to `CategoryDomain::$enabled` along with related methods (with negated value)
- `PaymentDomain` and `TransportDomain` are now created even for domains on which the entity should not be visible, check your custom queries that work with payments or transports
- instead of using `EntityManagerFacade::clear()` call `clear()` directly on the `EntityManager`
- all *Detail classes were removed:
    - use `CategoryWithLazyLoadedVisibleChildren` instead of `LazyLoadedCategoryDetail`
    - use `CategoryWithLazyLoadedVisibleChildrenFactory::createCategoriesWithLazyLoadedVisibleChildren()` instead of `CategoryDetailFactory::createLazyLoadedDetails()`
    - use `CategoryFacade::getCategoriesWithLazyLoadedVisibleChildrenForParent()` instead of `CategoryFacade::getVisibleLazyLoadedCategoryDetailsForParent()`
    - use `CategoryWithPreloadedChildren` instead of `CategoryDetail`
    - use `CategoryWithPreloadedChildrenFactory::createCategoriesWithPreloadedChildren()` instead of `CategoryDetailFactory::createDetailsHierarchy()`
    - use `CategoryFacade::getVisibleCategoriesWithPreloadedChildrenForDomain()` instead of `CategoryFacade::getVisibleCategoryDetailsForDomain()`
    - use `PaymentFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `PaymentDetail::$basePricesByCurrencyId`
    - use `TransportFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `TransportDetail::$basePricesByCurrencyId`
    - `ProductDetail::hasContentForDetailBox()` is not available anymore (it was useless)
    - use `ProductCachedAttributesFacade` for accessing product parameter values and selling price
    - in templates, use Twig function `getProductParameterValues(product)` instead of `productDetail.parameters`
    - in templates, use Twig function `getProductSellingPrice(product)` instead of `productDetail.sellingPrice`

### [shopsys/project-base]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

### [shopsys/coding-standards]
- create your custom `easy-coding-standard.yml` in your project root with your ruleset (you can use predefined ruleset as shown below) 
- in order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project`
- see [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
#### Example of custom configuration file
```yaml
#easy-coding-standard.yml
imports:
    - { resource: '%vendor_dir%/shopsys/coding-standards/easy-coding-standard.yml' }
parameters:
    exclude_files:
        - '*/ignored_folder/*'
    skip:
        ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff:
            - '*/src/file.php'
```

### [shopsys/product-feed-google]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
    
### [shopsys/product-feed-heureka]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
    
### [shopsys/product-feed-heureka-delivery]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
    
### [shopsys/product-feed-zbozi]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [From 7.0.0-alpha1 to 7.0.0-alpha2]
### [shopsys/project-base]   
- check changes in the `docker-compose.yml` template you used, there were a couple of important changes you need to replicate 
    - easiest way is to overwrite your `docker-compose.yml` with by the appropriate template 
- on *nix systems, fill your UID and GID (you can run `id -u` and `id -g` to obtain them) into Docker build arguments `www_data_uid` and `www_data_gid` and rebuild your image via `docker-compose up --build` 
- change owner of the files in shared volume to `www-data` from the container by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /var/www/html` 
    - the user has shared UID, so you will be able to access it as well from the host machine 
    - shared volume with postgres data should be owned by `postgres` user: `docker exec -u root shopsys-framework-php-fpm chown -R postgres /var/www/html/var/postgres-data` 
- if you were using a mounted volume to share Composer cache with the container, change the target directory from `/root/.composer` to `/home/www-data/.composer` 
    - in such case, you should change the owner as well by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /home/www-data/.composer` 

## Before monorepo 
Before we managed to implement monorepo for our packages, we had slightly different versions for each of our package, 
that's why is this section formatted differently.  

### [shopsys/product-feed-heureka]
#### From 0.4.2 to 0.5.0
- requires possibility of extending the CRUD of categories via `shopsys.crud_extension` of type `category`
- requires update of [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) to version `^0.3.0`
and [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface) to `^0.5.0`

#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.2.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-zbozi]
#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.3.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-heureka-delivery]
#### From 0.2.0 to 0.2.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.1.1 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-030)

### [shopsys/product-feed-interface]
#### From 0.4.0 to 0.5.0
- implement method `getMainCategoryId()` in your implementations of `StandardFeedItemInterface`.

#### From 0.3.0 to 0.4.0
- implement method `isSellingDenied()` for all implementations of `StandardFeedItemInterface`.
- you have to take care of filtering of non-sellable items in implementations of `FeedConfigInterface::processItems()` 
in your product feed plugin because the instances of `StandardFeedItemInterface` passed as an argument can be non-sellable now.
- implement method `getAdditionalInformation()` in your implementations of `FeedConfigInterface`.
- implement method `getCurrencyCode()` in your implementations of `StandardFeedItemInterface`.

#### From 0.2.0 to 0.3.0
- remove method `getFeedItemRepository()` from all implementations and usages of `FeedConfigInterface`.

#### From 0.1.0 to 0.2.0
- Rename all implementations and usages of `FeedItemInterface::getItemId()` to `getId()`.
- Rename all implementations and usages of `FeedItemCustomValuesProviderInterface` to `HeurekaCategoryNameProviderInterface`.
- If you are using custom values in your implementation, you need to implement interfaces from package [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (see [how to work with data storage interface](https://github.com/shopsys/plugin-interface#data-storage)).

### [shopsys/plugin-interface]
#### From 0.2.0 to 0.3.0
- all implementations of `DataStorageInterface` now must have implemented method `getAll()` for getting all saved data indexed by keys

### [shopsys/project-base]
#### From 2.0.0-beta.21.0 to 7.0.0-alpha1      
- manual upgrade from this version will be very hard because of BC-breaking extraction of [shopsys/framework](https://github.com/shopsys/framework)  
    - at this moment the core is not easily extensible by your individual functionality  
    - before upgrading to the new architecture you should upgrade to Dockerized architecture of `2.0.0-beta.21.0`  
    - the upgrade will require overriding or extending of all classes now located in  
    [shopsys/framework](https://github.com/shopsys/framework) that you customized in your forked repository  
    - it would be wise to wait with the upgrade until the newly build architecture has matured  
- update custom tests to be compatible with phpunit 7. For further details visit phpunit release announcements [phpunit 6](https://phpunit.de/announcements/phpunit-6.html) and [phpunit 7](https://phpunit.de/announcements/phpunit-7.html) 

#### From 2.0.0-beta.20.0 to 2.0.0-beta.21.0  
- do not longer use Phing targets standards-ci and standards-ci-diff, use standards and standards-diff instead 

#### From 2.0.0-beta.17.0 to 2.0.0-beta.18.0 
- use `SimpleCronModuleInterface` and `IteratedCronModuleInterface` from their new namespace `Shopsys\Plugin\Cron` (instead of `Shopsys\FrameworkBundle\Component\Cron`) 

#### From 2.0.0-beta.16.0 to 2.0.0-beta.17.0  
- coding standards for JS files were added, make sure `phing eslint-check` passes  
    (you can run `phing eslint-fix` to fix some violations automatically)  

#### From 2.0.0-beta.15.0 to 2.0.0-beta.16.0  
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface` instead  
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core  
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,  
    you will be prompted to fill it out again during `composer install`  
- all implementations of `StandardFeedItemInterface` must implement methods `isSellingDenied()` and `getCurrencyCode()`, see [product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040) 

### [shopsys/coding-standards]
#### From 3.x to 4.0
- In order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project --config=vendor/shopsys/coding-standards/easy-coding-standard.neon`
    - If you are overriding rules configuration in your project, it is necessary to do so in neon configuration file, see [example bellow](./example-of-custom-configuration-file).
    - See [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
##### Example of custom configuration file
###### Version 3.x and lower
```php
// custom phpcs-fixer.php_cs
<?php

$originalConfig = include __DIR__ . '/../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs';

$originalConfig->getFinder()
    ->exclude('_generated');

return $originalConfig;
```
###### Version 4.0 and higher
```neon
#custom-coding-standard.neon
includes:
    - vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
    - vendor/shopsys/coding-standards/shopsys-coding-standard.neon
parameters:
    exclude_files:
        - *_generated/*

```
[From 7.0.0-beta1 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta1...HEAD
[From 7.0.0-alpha6 to 7.0.0-beta1]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha6...v7.0.0-beta1
[From 7.0.0-alpha5 to 7.0.0-alpha6]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha5...v7.0.0-alpha6
[From 7.0.0-alpha4 to 7.0.0-alpha5]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha4...v7.0.0-alpha5
[From 7.0.0-alpha3 to 7.0.0-alpha4]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha3...v7.0.0-alpha4
[From 7.0.0-alpha2 to 7.0.0-alpha3]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha2...v7.0.0-alpha3
[From 7.0.0-alpha1 to 7.0.0-alpha2]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...v7.0.0-alpha2

[shopsys/shopsys]: https://github.com/shopsys/shopsys 
[shopsys/project-base]: https://github.com/shopsys/project-base 
[shopsys/framework]: https://github.com/shopsys/framework 
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi 
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google 
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka 
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery 
[shopsys/product-feed-interface]: https://github.com/shopsys/product-feed-interface 
[shopsys/plugin-interface]: https://github.com/shopsys/plugin-interface 
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards 
[shopsys/http-smoke-testing]: https://github.com/shopsys/http-smoke-testing 
[shopsys/form-types-bundle]: https://github.com/shopsys/form-types-bundle 
[shopsys/migrations]: https://github.com/shopsys/migrations 
[shopsys/monorepo-tools]: https://github.com/shopsys/monorepo-tools
[shopsys/microservice-product-search]: https://github.com/shopsys/microservice-product-search
[shopsys/microservice-product-search-export]: https://github.com/shopsys/microservice-product-search-export
