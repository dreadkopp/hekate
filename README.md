# KERBEROS

a configurable api gateway with included auth{entication,orization}

based on php, laravel, laravel/octane, laravel/sanctum

dynamically configure path and endpoint for all proxy endpoints via database

add users and clients as authenticable entities

TODO:

- [ ] remove unneeded modules from stack
- [ ] implement authorization via extended sanctum
- [ ] use sanctum roles to extend auth by regex matching of path
- [ ] reload/cache routes when manipulating routes + invalidate caches
- [ ] add admin mgmt interface