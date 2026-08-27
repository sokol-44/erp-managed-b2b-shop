# Request-to-template-to-class flow for one page type

This document traces a single page type through the legacy storefront architecture: the article page.

The goal is to show how a request flows from the URL, through the bootstrap, into a domain object, into a data class, and finally into the template output.

---

## 1. Entry: request routing

The page system relies on `com` values in the request and on the global framework object.

The bootstrap creates the core objects in [init.php](../init.php):

```php
$F = new Framework();
$Page = new Page();
$Lang = new Lang();
$Rights = new Rights();
$Data = new Data();
```

The request object is normalized by the `Framework` constructor in [inc/classes/Framework.php](../inc/classes/Framework.php), and the runtime routing values are stored in:

- `$F->GET`
- `$F->POST`
- `$F->REQUEST`
- `$F->com`

This is the request context used by page handlers.

---

## 2. The article page entry file

The actual article page is handled by [inc/com/article.php](../inc/com/article.php):

```php
$Article = new Article($F->GET['key']);
```

This is the key moment where the page loads the business object from the request.

The file then checks for presence:

```php
if( !$Article ) {
   $F->redirect( $F->make_link(CNF_DEFAULT_PAGE));
}
```

and assigns page context:

```php
$BC->add_crumb( array( 'name' => $Article->param['title'], 'path' => $F->make_link(CFG_COM_ARTICLE) ) );
$Page->head_title = $Article->param['title'];
```

This means the page is not just rendering static HTML; it is populating page metadata and breadcrumbs from the article object.

---

## 3. Domain class: `Article`

The domain object is defined in [inc/classes/Article.php](../inc/classes/Article.php).

The constructor loads the article by key:

```php
public function __construct($key = false) {
   self::$class = $this;
   $this->load_article($key);
}
```

`load_article()` calls:

```php
$this->param = Data::get_article($key);
```

The class stores the loaded data in `$this->param`, which is then consumed by the page template.

So the relationship is:

```text
request key -> Article constructor -> Data::get_article() -> database row -> Article->param
```

---

## 4. Data class: `Data_Article`

The SQL implementation is in [inc/classes/Data_Article.php](../inc/classes/Data_Article.php).

The relevant method is:

```php
static function get_article($key) {
   $query = 'select a.id_article, a.date_created, a.date_modified, a.title, a.content
   from ' . TBL_GLOBAL_ARTICLE . ' a
   where a.id_article = ' . db_int($key) . '';
   $result = db_query( $query );
   return db_fetch_array($result);
}
```

This method reads the article row from the configured DB table constant `TBL_GLOBAL_ARTICLE` and returns a single associative array.

The important connection is:

- `Article` defines the runtime object
- `Data_Article` provides the low-level SQL access
- [inc/functions/database.php](../inc/functions/database.php) handles actual database execution

---

## 5. Template rendering

Once the article object is created, the template section in [inc/com/article.php](../inc/com/article.php) renders output:

```php
<div class="article_container">
  <div class="article_container article_title container_header"><?php echo $Article->param['title']; ?><div class="icon"></div></div>
  <div class="article_container article_content"><?php echo $Article->param['content']; ?></div>
  <div class="article_container article_bottom container_bottom"></div>
</div>
```

This shows the final mapping:

- title comes from `Article->param['title']`
- content comes from `Article->param['content']`
- page metadata is set from the same model

The page is not retrieving directly from SQL in the template; the data is already prepared in the object.

---

## 6. Full request flow summary

The full path for an article request looks like this:

```text
URL / request with com=article and key=...
  -> bootstrap in [init.php](../init.php)
  -> Framework normalizes request values
  -> [inc/com/article.php](../inc/com/article.php) runs
  -> new Article($F->GET['key'])
  -> Article::__construct() loads the row
  -> Data::get_article() resolves to Data_Article::get_article()
  -> SQL query reads TBL_GLOBAL_ARTICLE
  -> row returned as associative array
  -> Article->param stores the data
  -> template reads Article->param['title'] and Article->param['content']
  -> HTML output is rendered
```

---

## 7. Reverse lookup for this page type

If you need to answer: “Where does this article page get its data from?” the reverse trail is:

1. See [inc/com/article.php](../inc/com/article.php)
2. Identify `new Article(...)`
3. Open [inc/classes/Article.php](../inc/classes/Article.php)
4. Find `load_article()` and `Data::get_article()`
5. Open [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)
6. Inspect the SQL method for the actual table and query
7. Confirm the data’s use in the template block in the same page file

This is the correct “reverse information” model for the legacy codebase: follow the object creation back to its data source and then back to the UI rendering.

---

## 8. Why this matters for maintenance

This page type is a good example of the project’s general engineering model:

- request routing is global and object-oriented only at the edges
- data is loaded into domain objects
- the template reads those object properties, not the database directly
- the real persistence logic sits in `Data_*` query methods

That means refactoring the page safely requires reading all three layers together:

- request/controller layer: [inc/com/article.php](../inc/com/article.php)
- domain model layer: [inc/classes/Article.php](../inc/classes/Article.php)
- data layer: [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)

---

## 9. Summary

The article page is an ideal model for the repo’s design:

- the request is normalized by the framework
- the page instantiates a domain object
- the domain object asks the data layer for data
- the data layer executes the SQL query
- the template renders the loaded object fields

This is the clearest “request-to-template-to-class flow” in the project and a good reference for tracing other page types as well.
