# Glossary

The purpose of this document is to keep reference of terms used by Product or Development that differ in their meaning to the other party, are not directly used by the other party, or otherwise benefit from additional context.

A term should be cataloged here if:

- It is wholly not used in the same way by both parties
- It is a term used by development that should NEVER appear in User facing UI
- We have changed the name of something in the Product UI for any reason and will not or have not yet updated code and/or database schema
- The team would otherwise benefit from having a record of it providing context

## Product Terms

Terms used by product, stakeholders, and in the UI, mapped to their code / development understanding equivalents.

**Concern**
References the `app-modules/concern/src/Models/Concern.php` Model.

Used to be called "Alert". Much of the code has been renamed to use the term "concern". But database schema has NOT yet changed.

## Development Terms

Terms used by development mapped to their product / stakeholder understanding equivalents.

**Educatable**
Largely based on the Interface shared by `Student` and `Prospect` models. Development will often refer to "Educatable" when talking about something that could possibly be either a Student or Prospect, such as in polymorphic relationships.

Product is aware of this term and does understand its meaning and as such it can be used in interdepartmental discussion.

NEVER use this term in User facing UI, it is not something that is pertinent to or understood by Users
