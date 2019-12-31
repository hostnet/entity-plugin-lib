entity-plugin-lib
=================

### The problem

The Hostnet Entity Composer plugin was developed to solve several problems experienced with plain usage of the Doctrine ORM. Although our solution is inspired on working with doctrine it is perfectly usable for every other way of persisting entities. The problems we came across are:
- Entities became really big;
- Entities were shared between applications and these applications inherited functionality they were not allowed to use;
- Knowledge areas like calculating discounts were spread through the model and not grouped together;
- Misuse of inheritance to prevent code duplication.

To solve our problem we borrowed some ideas from another realm of software development, since we felt we were hitting the boundaries of [Object Oriented Programming][oop] and sought for more modularization. We used the ideas behind [Aspect Oriented Programming][aop] to solve our problems.

The basic concept: Entities are grouped by responsibility in packages. This plugin links the packages together.

This is awesome for you if;
- You maintain an open source project that includes entities, that you would like people to expand on.
- You have a database and various applications accessing different (but related) subsets of that database.
- Or searching to glue something yourself.

If you do not belong in one of those groups, be careful since this might not be what you want.

### Example

One application needs to know about ```Clients```, but is unaware of the concept of a ```Contract```. Another application requires access to both of them. So in one application, you'd want to be able to call ```$client->getContracts()```. But in the other, you'd prefer to not know about contracts at all!

With this plugin you can create one package that is purely ```Client``` focussed, and another that is ```Contract``` focussed, and that injects the additional functionality (like ```getContracts```) to ```Client```.

### Usage

#### Creating an extendable package

- Create a composer package to put your entities in.
- Use PSR-0 or PSR-4 [autoloading][autoload] for the src/ directory.
- The package should be of [type][type] ```hostnet-entity```.
- The package should require ```"hostnet/entity-plugin-lib": "2.*"```
- Create one entity, say ```Page```, inside a namespace that ends with ```Entity```.
- Run ```php composer.phar dump-autoload```. (The entity plugin hooks to this event.) This should cause some output:
```
Pass 1/3: Generating compound traits and interfaces
Pass 2/3: Preparing individual generation
Pass 3/3: Performing individual generation
```
- From your entity, use the trait generated in the ```Generated/``` namespace:
```
    use \Hostnet\Page\Entity\Generated\PageTraits;
```

Congratulations, you now have an extendable entity package.

#### Extending the package

Follow the steps above, and
- Make sure your new package depends on the old one.
- Instead of creating the ```Page``` class, create a ```PageTrait```.
- Run ```php composer.phar update```.
- Check ```vendor/your-vendor-name/your-package-name/src/Entity/Generated/PageTraits.php```. It should include a reference to your package.

### Tips

If you run the composer.phar with ```-v``` or ```-vv``` or ```-vvv``` it will show more information.

If you want to extend an entity from your main application, you can use the ```entity-bundle-dir``` setting in the extra section of your composer.json.

If you do not want to generate interfaces, you can set the ```generate-interfaces``` setting to ```false``` in the extra setting of your composer.json. 
We will change this to being the default behaviour in a future release.

For a quick overview have a look at the [cheatsheet][cheatsheet].

[aop]: http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.95.2500&rep=rep1&type=pdf "Aspect-oriented programming"
[oop]: http://148.204.64.201/paginas%20anexas/POO/papers/papers%20de%20POO/p96-pokkunuri.pdf "Object Oriented Programming"
[composer]: http://getcomposer.org/doc/00-intro.md
[type]: https://getcomposer.org/doc/04-schema.md#type
[autoload]: https://getcomposer.org/doc/04-schema.md#autoload
[cheatsheet]: https://hostnet.github.io/entity-plugin-lib/
