# Infection Meetup

This session is an introduction to **[mutation testing](https://en.wikipedia.org/wiki/Mutation_testing)** and why you should invest some time to run these tests. For [PHP](https://secure.php.net) projects we're using [Infection](https://infection.github.io/), the PHP Mutation Framework.

## Introduction

Mutation Testing is a testing practice that will evaluate the quality of existing unit tests to see if they are resistent to small modifications in the source code. If they are solid they will fail these changes and are resistent against mutations, if designed poorly (both in code as in tests) they will pass and thus fail the mutation test, exposing potential quality and security issues in your code.

There are some definitions you need to understand before we continue:

- **Mutation:** a change in the source code
- **Mutant:** each version of change of that particular source code
- **Killing the mutant:** rejecting the change or marking the test as failed for this particular change

## The application under test

We have a small application that interacts with the [Meetup.com API] and will list the event's participants and displays how many groups they are part of and how many meetups they have attended.

We're using [Guzzle](https://github.com/guzzle/guzzle) to connect with the [Meetup.com API] and interact with the endpoints. Since this application is developed in a [Test-Driven Development (TDD)](https://en.wikipedia.org/wiki/Test-driven_development), when you look in the GIT log you'll see my tests being developed first before the code is added.

The application is devided in 4 main components:

- **Consumer:** Provides an interface between Guzzle and our logic
- **Event:** Is the logic for processing Meetup.com events
- **Member:** Is the logic for processing Meetup.com members
- **Group:** Is the logic for processing Meetup.com groups

[Meetup.com API]: https://www.meetup.com/meetup_api/