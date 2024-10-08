<!doctype html>
<html>
<head>
    <meta charset="uft-8">
    <meta name="author" content="Richard Soares">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightsail MVC for PHP :: Documentation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <?php load_style(['reset','prism','main']) ?>
</head>
<body>
<?php extend_view(['common/header'], $data) ?>

<p><a href="/">Go Home</a></p>

<h2>Preface</h2>
<p>
    A model-view-controller (MVC) framework is a design
    pattern for successfully and efficiently relating the user interface to underlying data models.
</p>
<p>
    The MVC pattern has been heralded by many developers as a useful pattern for the reuse of object code and a pattern
    that allows them to significantly reduce the time it takes to develop applications with user interfaces.
</p>
<p>
    The model-view-controller pattern has three main components:
</p>
<ul>
    <li>A <strong>Model</strong> , which represents the underlying, logical structure of data in a software application.
        The object model does not contain any information about the user interface.</li>
    <li>A <strong>View</strong> , which is a collection of classes representing the elements in the user interface (those elements the
        user can see and respond to on the screen, such as buttons, display boxes, and so forth).</li>
    <li>A <strong>Controller</strong> , which represents the classes connecting the model and the view, and is used to communicate 
        between classes in the model and view.</li>
</ul>

<a id="requirements"></a>
<h2>Requirements</h2>
<ul>
    <li>A general understanding of Object-Oriented Programming using PHP</li>
    <li>The included Docker Container will build a LAMP Stack for PHP. </li>
</ul>
<br>
<p>If you develop a new Core Class (see: /app/core) you must initialize it here: <strong>/app/init.php</strong></p>

<a id="start"></a>
<h2>Getting Started</h2>
<p>
The root of this installation contains the following files for spinning up a local Docker Container on your computer.
    While this is not a requirement, you can deploy this Docker Container on any compatible web hosting environment.
</p>
If you have Docker installed, then you can...
<ul>
    <li>
        <ul>
            <li>Run "<strong>docker compose up</strong>" in your Terminal from the directory containing <strong>app/docker-compose.yml</strong> file.
            <li>Open a web browser and go to: <a href="http://localhost"><strong>http://localhost</strong></a>
            <li>You do not have to stop/start the Docker container while editing code. Code updates are reloaded in realtime.
        </ul>
</ul>

<a id="setup"></a>
<h2>Setting up the database</h2>
Lightsail can set up the tables and data into the MySQL database required for this documentation.
<ul>
    <li>Open a web browser and got to: <a href="http://localhost/setup"><strong>http://localhost/setup</strong></a></li>
</ul>


<a id="features"></a>
<h2>Features of this MVC Framework</h2>
<ul>
    <li>
        <strong>Easy Configuration</strong> with the use of individual config files, like database, routes, and so on.
    </li>
    <li>
        <strong>Routing</strong> which is the method in which you specify the URLs that will load your pages, for example:
        <ul>
            <li>Get a a list of users: http://www.example.com/user/list</li>
            <li>Get a product: http://www.example.com/product/id/123</li>
            <li>Call and API for report data: http://www.example.com/api/v1/sales-report</li>
        </ul>
    </li>
    <li>
        A <strong>Base Model</strong> which serves as an abstract to PHP-PDO.
    </li>
    <li>
        An <strong>Organized Directory Structure</strong> how public access is separated from the core application.
    </li>
    <li>Support for <strong>Multiple Languages</strong> specified by URL's, like: www.domain.com/<strong>en</strong>/user/123</li>
<pre><code class="language-text">root
    /app
        /config
            /config.php - app configuration
            /database.php - settings to connect to your database
            /routes.php - custom URL routing patterns
        /controllers
            Docs.php - the controller serving up this documentation
        /core
            App.php - all the magic
            Controller.php - Base controller
            Model.php - Base model
        /helpers
            view.php
        /languages
            en_lang.php
            fr_lang.php
            sp_lang.php
        /models
            Users.php - a sample data model
        /views
            /common
                footer.php - footer of this page
                header.php - header of this page
            /docs
                index.php - this page
        init.php
    /public
        /css - your styles
        /js - your scripts
        index.php - the front-loader and environment configurations
        .htaccess - URL routing to the front-loader
    </code></pre>
    </li>
</ul>

<a id="routing"></a>
<h2>URL Routing</h2>
<p>
    By default, a URL has a one-to-one relationship to the Controller and Method called which has the following format:

</p>
<pre><code class="language-text">http://example.com/controller/method/param1/param2</code></pre>
<p>
In some instances, however, you may want to remap this relationship so that a different class/method can be
called instead of the one corresponding to the URL. For example, let’s say you want your URLs to have this format:
<pre><code class="language-text">example.com/product/1/
example.com/product/2/
example.com/product/3/
example.com/product/4/
</code></pre>
</p>
<p>
Normally the second segment of the URL is reserved for the method name, but in the example above it instead
has a product ID. To manage custom routes you can remap the URI handler.
</p>
<p>
    <strong>NOTE</strong> it is not a requirement that you pass all parameters in the URL. You can create URL routes
    having only a controller/method/ pattern and provide data via HTTP POST, for example, coming from a form.
</p>
<p>
    You can add your custom routes to the routes configuration file located here: <strong>/app/config/routes.php</strong>
</p>
<p>
<strong>WILDCARDS</strong><br>
A typical wildcard route might look something like this:
<pre><code class="language-php">$route['product/:num'] = 'catalog/product_lookup/$1';</code></pre>
</p>
<p>
In a route, the array key contains the URL to be matched, while the array value contains the destination it should
be re-routed. In the above example, if the literal word “product” is found in the first segment of the URL, and a
number is found in the second segment, the “catalog” class and the “product_lookup” method are instead used.
</p>
<p>
You can match literal values, or you can use two wildcard types:
(:num) will match a segment containing only numbers. (:any) will match a segment containing any character
(except for ‘/’, which is the segment delimiter).
</p>
<p>
<strong>REGULAR EXPRESSIONS</strong><br>
If you prefer you can use regular expressions to define your routing rules. Any valid regular expression is allowed,
as are back-references. If you use back-references you must use the dollar syntax rather than the double backslash syntax.
</p>
<p>
    A typical RegEx route might look something like this:</p>
<pre><code class="language-php">$route['products/([a-z]+)/(\d+)'] = '$1/id_$2';</code></pre>
<p>
    In the above example, a URI similar to products/shirts/123 would instead call the “shirts” controller class
and the “id_123” method.
</p>
<p>
<strong>NOTE:</strong><br>
    <ul>
    <li>
        Routes will run in the order they are defined. Higher routes will always take precedence over lower ones.
    </li>
    <li>
        Route rules are not filters! Setting a rule of e.g. ‘foo/bar/(:num)’ will not prevent controller Foo and method bar
        to be called with a non-numeric value if that is a valid route.
    </li>
</ul>
</p>
<p>
<strong>IMPORTANT!</strong> Do not use leading/trailing slashes.
</p>
<p><strong>EXAMPLES:</strong></p>
<pre><code class="language-php">$route['journals'] = 'blogs';</code></pre>
<p>
A URL containing the word “journals” in the first segment will be remapped to the “blogs” controller class.
</p>
<pre><code class="language-php">$route['product/(:any)'] = 'catalog/product_lookup/$1';</code></pre>
<p>A URL with “product” as the first segment, and anything in the second will be remapped to the “catalog” controller class
and the “product_lookup” method.
</p>
<pre><code class="language-php">$route['product/(:num)'] = 'catalog/product_lookup_by_id/$1';</code></pre>
<p>A URL with “product” as the first segment, and a number in the second will be remapped to the “catalog” controller class and
the “product_lookup_by_id” method passing in the match as a variable to the method.
</p>

<a id="models"></a>
<h2>Models</h2>
<p>
    Models that you create must be stored in the <strong>/app/models/</strong> directory and MUST use a <strong>CamelCase.php</strong>
    file naming format. The Class name MUST also be named identically as the file name like so:
</p>
<pre><code class="language-php">class CameCase extends Model
{ .. }</code></pre>
<p><strong>Important!</strong> Do not name a Controller file or it's Class name the same as a Model
    as the names will Collide. A tip to remember is to name your Controller singular <strong>User</strong>
    and your Model plural <strong>Users</strong> typically helps avoid this issue.</p>
<p>
    The base Model serves as an abstract to PDO and can be extended by any custom Model. The base Model will handle
    all the heavy lifting to create a proper PDO database query and return results, if any.
</p>
<p>
    The Base Model located here <strong>/app/core/Model.php</strong> can be extended by your custom models like so:
</p>
<pre><code class="language-php">class User extends Model
{
    private $db;

    public function __construct()
    {
       $this->db = new Model();
    }

    public function getUsers()
    {
        /* get all users */
        $results = $this->db->select("users");
    }

    public function getMaleUsers()
    {
        /* get a specific user */
        $results = $this->db->select("users", "Gender = 'male'");
    }

    public function addUser($fname, $lname, $age, $gender)
    {
        /* add a new user */
        $data = [
                "fname"  => $fname,
                "lname"  => $lname,
                "age"    => $age,
                "gender" => $gender
                ];
        $this->db->insert("users", $data);
    }
}
</code></pre>
<p>
    The Base Model class is fully commented providing all the standard Create, Read, Update, Delete functionality.
</p>
<p>
    <strong>Important!</strong> The Base Model performs automatic mapping of table columns names to the key-names of
    the data array you pass into it.  Any key-names not matching a table column name will be dropped.
</p>

<p>
    <strong>Selecting Records:</strong> There are two methods available for selecting records.
</p>
<p>
<strong>select()</strong> - Use this method to return multiple records. For example:
</p>
<pre><code class="language-php">// Use select() to return multiple records.
$users = $user->select('users','dept = :dept', [":dept => 12"]);
$print_r($users);
// OUTPUT:
array(
    [0] => array('name' => 'Bob', 'dept' => '12'),
    [1] => array('name' => 'Mary', 'dept' => '12'),
    [2] => array('name' => 'Sue', 'dept' => '12'),
)

// Loop thru a set of records.
foreach($users as $user) {
    echo "Employee: " . $user['name'] . "\r\n";
}
// OUTPUT
Employee: Bob
Employee: Mary
Employee: Sue
</code></pre>
<br>

<p>
    <strong>selectOne()</strong> - Use this method to return a single record. For example:</li>
</p>
<pre><code class="language-php">// Use selectOne() to return a single record.
$user = $user->selectOne('users','id = :id', [":id => 12"]);
$print_r($user);
// OUTPUT:
array('name' => 'Bob Smith', 'id' => '12')

// Display values from this single record.
echo "Welcome " . $user['name']; 
// OUTPUT
Welcome Bob Smith
</code></pre>

<p>
    <strong>selectExtended()</strong> - Use this method when custom SQl is required. For example:</li>
</p>
<pre><code class="language-php">// Use selectOne() to return a single record.
$user = $user->selectExtended('select name from users where id = :id',["id:" => 12]);
$print_r($user);
// OUTPUT:
array('name' => 'Bob Smith', 'id' => '12')

// Display values from this single record.
echo "Welcome " . $user['name'];
// OUTPUT
Welcome Bob Smith
</code></pre>

<a id="views"></a>
<h2>Views</h2>
<p>
    Views are the presentation part of the MVC pattern and as such they define design/layout of the page.
    A typical View might look like the following:
</p>
<p>
Sometimes you may have reusable parts of your page such as a header and footer. The View Helper loads by default and
    allows you to "extend" your View. In this example, we are adding the common header and footer View fragments by specifying
    their location in the sub-directory called "common" within the Views directory, located here: <strong>/app/views/common/</strong>
</p>

<pre><code class="language-php">extend_view(['common/header'], $data)
... the main body of your html page ...
extend_view(['common/footer'], $data)</code></pre>
<p>
    The second optional parameter <strong>$data</strong> is used to pass an array collection of data to this view fragment.
</p>
<pre><code class="language-php">load_style(['reset','main'])</code></pre>
<p>
    The <strong>load_style()</strong> function will load a list of CSS style files located in your public directory here:
    <strong>/app/public/css/</strong> *You do not need to specify the file extension ".css"
</p>

<pre><code class="language-php">load_script(['main','other']);</code></pre>
<p>
    The <strong>load_script()</strong> function will load a list of Javascript files located in your public directory here:
    <strong>/app/public/js/</strong> *You do not need to specify the file extension ".js"
</p>
<p>
    <strong>NOTE</strong>
    <ul>
    <li>You do not need to specify the file extension of the view but the View MUST be a PHP file.</li>
    <li>You can create any directory organizational structure under the <strong>/app/views/</strong> directory
        so long that you specify the path when loading a View from the Controller or extending it within the View, for example:</li>
</ul>

</p>
<pre><code class="language-php">extend_view(['reports/daily/common/header'], $data)
extend_view(['reports/weekly/common/header'], $data)</code></pre>
<p>

<p>
For a comprehensive example of passing data into a View, see: <a href="/examples/passing_data">Example for passing values into Views</a>.
</p>

<a id="controllers"></a>
<h2>Controllers</h2>
<p>
    To understand how Controllers work we need to back up a little bit and recall how we format a URL. For this example
    lets say we need to query information about a user and display the information on a report.
</p>
<pre><code class="language-text">http://www.acme.com/user/report/123</code></pre>
<p>
    Our URL could have this form, where we are requiring the "User" Controller class and passing into the "report" method the value of "123" (the user id).
</p>
<p>Our Controller might look like the following:</p>
<pre><code class="language-php">
class User extends Controller {

    public function __construct()
    {
        // Load the View Helper other methods in the Class will need.
        $this->load_helper(['view']);
        // You can also preload Models as properties of a Controller Class 
        // if they are frequently used by Methods, however, it's more efficent 
        // to instanciate a Model from within the method, as shown below.
    }

    // Load the Home Page if the URL does not specify a method
    public function index()
    {
        $this->view('home/index');
    }

    // Get some User data and pass it into the View "user_profile".
    public function report($user_id)
    {
        // Load the User Model so that we can query the database
        $user = $this->model('User');

        // Get this user. 
        // Use the selectOne() method for single record returns.
        $bind = [':id' => $user_id];
        $record = $user->selectOne('users','id = :id', $bind);

        // Prepare the values we will pass into the View.
        $data = [
            'users' => $record;
            'is_admin' => 'yes' // some other arbitrary value.
        ];

        // Load the View. 
        // The second attribute $data is optional and only required
        // when passing data into a View.
        $this->view('reports/user_profile', $data);
    }
}
</code></pre>
<br>
<p>
    Let's assume that we received back the following data when accessing http://my-domain.com/user/report
</p>
<pre><code class="language-text">['name' => 'Bob Smith', 'age' => '24', 'email' => 'bobsmith@example.com']</code></pre>
<br>
<p>
    Within your View file you could access these values in one of two ways:
    <ul>
    <li>As the key-name of the $data array,</li>
    <li>or as a magically-generated PHP variable.</li>
</ul>
</p>
<pre><code class="language-php">echo $data['name'] // outputs "Bob Smith"
echo $name // also outputs "Bob Smith"</code></pre>
<p>
    <strong>Why offer both options?</strong> because developers have preferences on style.
</p>

<p>
For a comprehensive example of passing data into a View, see: <a href="/examples/passing_data">Example for passing values into Views</a>.
</p>

<p>
    <strong>Note:</strong>
    <ul>
    <li>When specifying a Controller class such as "UserHistory" you must hyphenate the class name in the URL like
        so <strong>/user-history/</strong>method/param1/param2</li>
    <li>If your Controller class is located in a sub-directory within the <strong>/apps/controllers/</strong> directory
        you must specify it in the URL like so <strong>/directory/user-history/</strong>method/param1/param2 *</li>
</ul>
* However, you may also use custom routing to hide a sub-directory. See the <a href="#routing">Routing section</a> above.
</p>

<a id="sessions"></a>
<h2>Sessions</h2>
<p>
Session data can be managed using the Session Model within your Controllers making this data persist between browser sessions. 
</p>
<p>
The Session Model will initially check if a Session Cookie exists, and if so, the PHP Session will be loaded with the data stored
in the database. If no Session Cookie exists, then a new Session database record and cookie will be generated.
</p>

<p>
    <strong>Setting up your database:</strong>
<ul>
    <li>Apply your database configurations here <strong>/app/config/database.php</strong>  </li>
    <li>Create the following MySQL table in your database. </li>
</ul>
</p>

<pre><code class="language-text">
CREATE TABLE sessions (
        session_id CHAR(32) NOT NULL,
        session_data TEXT NOT NULL,
        session_lastaccesstime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (session_id)
    );
</code></pre>
<br>
<p>
    <strong>Run the following example from here:</strong> <a href="/docs/session">/docs/session</a>, then reference:
<ul>
<li>/app/controllers/Docs/Docs.php - function session()
<li>/app/models/Sessions.php
</ul>

</p>

<pre><code class="language-php">
class Docs extends Controller() 
{
    public function session()
    {
        // Add the following line to enable database sessions.
        // You do NOT need to call session_start() before using PHP sessions.
        $session = $this->model('Session');

        // Use PHP Sessions like normal.
        $_SESSION['fname'] = 'Walter';
        $_SESSION['lname'] = 'Smith';
        $_SESSION['title'] = 'Sales Manager';

        // For debugging needs, use the getSessionData() function.
        echo "GET ALL SESSION DATA:";
        print_r($_SESSION);

    }
}
</code></pre>
<br />
<p>Loading the url (http://localhost/docs/session) will generate the following output:</p>

<pre><code class="language-text">
GET ALL SESSION DATA:
<?php
print_r($_SESSION);
?>
</code></pre>

<br>
<strong>About Buggy Sessions using the Google Chrome Browser</strong>
<ul>
    <li>Issue 1: The Sessions table described above will record empty records.</li>
    <li>Issue 2: The website may appear to loose your Session cookie (aka: PHPSESSID).</li>
    <li>Cause: There is a known bug in Chrome that will loose track of your Session Cookie as a result of not having a fav.ico icon.</li>
    <li>Effect: When Chrome does not receive the fav icon upon request Chrome destroys the current Session cookie (aka: PHPSESSID).</li>
    <li>Solution: Add a fav.ico file into the Public root directory.</li>
</ul>


<a id="logs"></a>
<h2>Message Logs</h2>

<p>There is an optional use Model for logging messages from your scripts called <strong>Log</strong> located here: /app/models/Log.php</p>
<p>The Log Model will store your message in a database table called <strong>logs</strong>.
    That table should be created in your database using the following SQL statement:</p>

<pre><code class="language-text">
CREATE TABLE logs
(
    event_type     varchar(45)                        NULL,
    event_message  varchar(255)                       NULL,
    event_script   varchar(255)                       NULL,
    event_datetime datetime DEFAULT CURRENT_TIMESTAMP NULL
);
</code></pre>

<h3>Local Log Files</h3>
<p>If you set ENVIRONMENT = 'localhost' in your local <strong>.env</strong> file, your messages will also be written locally into a directory here: <strong>/app/logs/</strong></p>
<p>These locally written log messages come in handy when working on your local computer and not require access to the logs table in your database.</p>

<h3>How to implement Message Logging</h3>

<p>You send a message to the Log using teh following format within your PHP scripts where you want a message to be recorded.</p>

<pre><code class="language-text">
$this->log->addRecord($event_type, $event_script, $event_message);
</code></pre>

<p>The values you must pass into the Model function <strong>addRecord()</strong> are:</p>

<ul>
    <li><strong>$event_type</strong> - this can be any custom classification, like error, success, warning, etc.</li>
    <li><strong>$event_script</strong> - this is the name of the script (file) that sent the message.</li>
    <li><strong>$event_message</strong> - The message. A good format to use is -> ClassName::MethodName::message</li>
</ul>

<br>
<p>If you prefer to record messages logs on your local filesystem while developing, you can call the
    following Model function. However, this function is called by default when using addRecord().</p>

<pre><code class="language-text">
$this->log->writeMessageToLocalLogFile($event_type, $event_script, $event_message);
</code></pre>

<p>The values you pass into this function are the same as those for the addRecord().</p>

<h3>For example</h3>
<p>The following output is a dump of all Log records from the MySQL database called "Logs".
    This data was loaded into the database for this demo when you performed teh database Migration step.</p>

<pre><code class="language-text">
<?php
print_r($logs);
?>
</code></pre>

<a id="API Interfaces"></a>
<h2>API Interfaces</h2>
<p>Information about creating a API Interface <a href="/docs/api">can be found here.</a></p>

<a id="language"></a>
<h2>Language Dictionaries</h2>
<p>Information about creating a Multi-Language application <a href="/docs/language">can be found here.</a></p>

<a id="redis"></a>
<h2>Using Redis, the in-memory cache</h2>
<p>Information about implementing the <strong>Redis Model</strong> (<a href="https://redis.io">https://redis.io</a>) <a href="/docs/redis">can be found here.</a></p>


<a id="rabbitmq"></a>
<h2>Using RabbitMQ, the message broker</h2>
<p>Information about implementing the <strong>RabbitMQ Model</strong> (<a href="https://www.rabbitmq.com">https://www.rabbitmq.com</a>) <a href="/docs/rabbitmq">can be found here.</a></p>

<a id="s3"></a>
<h2>Using AWS S3 Object Storage Service</h2>
<p>Information about implementing the built-in <strong>AWS S3 Object Storage Service Model</strong> <a href="/docs/aws_s3">can be found here.</a></p>


<a id="s3"></a>
<h2>Geolocation and Address Verification</h2>
<p>Information about implementing the built-in <strong>US Census GeoCoder API</strong> <a href="/docs/geocoder">can be found here.</a></p>


<?php extend_view(['common/footer'], $data) ?>
<?php load_script(['prism', 'main']); ?>
</body>
</html>