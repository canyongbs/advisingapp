---
status: 'proposed'
date: 2026-01-16
decision-makers: Kevin Ullyott, Dan Harrin, Payal Baldaniya
consulted: Kevin Ullyott, Dan Harrin, Payal Baldaniya
---

# How to Ensure Case-Sensative String Comparision in Postgres

## Context and Problem Statement

By default in Postgres, strings are compared case-insensative, meaning "ABC" != "abc".

This is problematic when we NEED some things to be case-sensative. For example, if we need email addresses in the system to be unique and we want to consider "Example@email.com" to violate "example@email.com".

Though there may be areas where this is managed ad hoc, there is a desire for there to be a general strategy for enabling this functionality

## Considered Options

- citext data type
- Functional (Expression) Index
- Manual handling in code/queries

## Decision Outcome

Chosen option: "{title of option 1}", because {justification. e.g., only option, which meets k.o. criterion decision driver | which resolves force {force} | … | comes out best (see below)}.

<!-- This is an optional element. Feel free to remove. -->

### Consequences

- Good, because {positive consequence, e.g., improvement of one or more desired qualities, …}
- Bad, because {negative consequence, e.g., compromising one or more desired qualities, …}
- … <!-- numbers of consequences can vary -->

<!-- This is an optional element. Feel free to remove. -->

### Confirmation

{Describe how the implementation of/compliance with the ADR can/will be confirmed. Are the design that was decided for and its implementation in line with the decision made? E.g., a design/code review or a test with a library such as ArchUnit can help validate this. Not that although we classify this element as optional, it is included in many ADRs.}

<!-- This is an optional element. Feel free to remove. -->

## Pros and Cons of the Options

### {title of option 1}

<!-- This is an optional element. Feel free to remove. -->

{example | description | pointer to more information | …}

- Good, because {argument a}
- Good, because {argument b}
  <!-- use "neutral" if the given argument weights neither for good nor bad -->
- Neutral, because {argument c}
- Bad, because {argument d}
- … <!-- numbers of pros and cons can vary -->

### {title of other option}

{example | description | pointer to more information | …}

- Good, because {argument a}
- Good, because {argument b}
- Neutral, because {argument c}
- Bad, because {argument d}
- …

<!-- This is an optional element. Feel free to remove. -->

## More Information

{You might want to provide additional evidence/confidence for the decision outcome here and/or document the team agreement on the decision and/or define when/how this decision the decision should be realized and if/when it should be re-visited. Links to other decisions and resources might appear here as well.}
