# Faulancer Framework

The Faulancer Framework is a experimental alternative to all the huge 
Frameworks out there. The goal is, to make it as simple as possible for 
the developer to get his job done.

In order to achieve such a goal, one thing is to combine all the common known 
patterns which are already widely used and are proven. For example the 
application structure is very similar to Symfony. The framework also offers 
ViewHelpers, which was a very useful feature then within the Zend Framework.

On top of that, everything should follow standards. That means, the framework 
uses PSR-compatible Interfaces where possible.

Currently there is support for the following Standards:

- PSR-3 (Logger Interface)
- PSR-4 (Autoloading)
- PSR-7 (HTTP Message Interface)
- PSR-11 (Container Interface)
- PSR-17 (HTTP Factories)
- PSR-18 (HTTP Client)

In the near future, following standards are planned to be implemented:

- PSR-14 (Event Dispatcher)
- PSR-16 (Simple Cache)