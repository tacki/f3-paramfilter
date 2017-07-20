# f3-paramfilter
**Parameter Filtering Plugin for the PHP Fat-Free Framework**

This plugin is build for [Fat-Free Framework](http://www.fatfreeframework.com/). To learn more about Fat-Free Framework, visit their Homepage or the [F3 Git Repository](http://github.com/bcosca/fatfree)

* [Installation](#installation)
* [Usage](#usage)
* [ParamFilter Class methods](#paramfilter-class-methods)

## Installation

To install this plugin, just copy the `lib/ParamFilter.php` into your F3 `lib/` or your `AUTOLOAD` folder.

## Usage

This simple Plugin uses Regex to filter all Usercontent coming to the Framework. This includes POST and GET-Values and also Tokens in URLs.

If you use .ini-Files for your Routing, the usage is pretty forward as you can see in this Example

```ini
[routes]
GET /@foo=Controller->main

[filter.PARAMS]
foo="[a-zA-Z]+"
```

We defined a route with a token called 'foo' and also a filter for this token to only allow alphabetic characters.
The next Step is to let the ParamFilter-Class check the Filter for you. I recommend the beforeRoute() method, 
Controller->beforeRoute in this Example.

```php
class Controller {
    ...
    public function beforeroute($f3)
    {
        if(!\ParamFilter::instance()->checkFilter('PARAMS')) {            
            $f3->error(400, 'Bad Token value');
        }
    }
    ...
}
```

You can also use this for Forms:
```html
<form action="/formvalidate">
  First name:<br>
  <input type="text" name="firstname" value="Mickey"><br>
  Last name:<br>
  <input type="text" name="lastname" value="Mouse"><br><br>
  <input type="submit" value="Submit">
</form> 
<?php
$f3->set('filter.POST.firstname', '[A-Za-z]+');
$f3->set('filter.POST.lastname', '[A-Za-z]+');
?>
```

And in the /formvalidate-route:
```php
if(!\ParamFilter::instance()->checkFilter('POST')) {            
    // print an error or redirect or do something else
}
```

## ParamFilter Class Methods
```php
// Check all available Filters
public function checkAllFilters() : bool;
// Check a specific Filter (PARAMS,GET,POST,...)
public function checkFilter($type) : bool;
```