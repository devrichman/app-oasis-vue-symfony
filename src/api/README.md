# API

## Clean architecture 

Our project is based on clean architecture. 

Clean architecture uses three principle repositories which are : 

**Application** 

In this repo, we describe all use cases. A use case is created per action  (action that could be done by user). In a use case, we do only functional verification which means only rules related to business. 

**Domain**

In this repo, we have our :
  - Model : In every Entity, we need to do some validations related to the data. Example : If the firstname for user should be a string of 255 characters maximum, we need to call the InvalidStringValue exception that will be thrown if the format is not respected. For every model, implement LoggableModel that will allow us to set and get created by / at / updated by / at attributes simply. 
  - Exceptions : We have different exceptions. if you need to add a validation of string such as phone regex, add it to this exception. All exceptions should implement DomainException 
  - Enum : Our constants are all described in Enum 
  - Repository : In this repo, we create interfaces per model, in order to describe the methods that could be used to update / get data. These repositories must be implemented by DAOs. In Use Cases, we can call these methods. Example : If we want to add a role to a user, first, we need to check if user exists so we must find a user by id and so we call the method mustFindOneById in userRepository to tell the use case that we must find a user by its id. 


**Infrastructure** 
- Commands : We have our commands. We started by creating a new command to create the super admin user. 
- Controller : A controller per use case must be created 
- DAO : we have our generated Daos that we can edit in order to implement Repositories and implements the methods of interfaces. 
- Logging : Here we have ModelLogger which a “trait” that allows to reuse the code of setting creation information such as user and date. In order to use the trait you just need to add “use ModelLogger” in your class. 
- Migrations : contains our database migrations 

**Unit tests**

We have unit tests that are structured the same way as the whole application :
- Application : Here we test use cases 
- Domain : Here we test our models, so we test the data 

##Scheduled treatments 

We have some scheduled treatments in order 
