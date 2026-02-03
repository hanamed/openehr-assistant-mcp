# openEHR Archetype Design Principles

**Scope:** Foundational principles for openEHR archetype modelling, community modelling practices
**Keywords:** archetype, design, principles, modelling, foundation

---

## Archetype as Formal Domain Content Model

An archetype is a formal, constraint-based expression of a domain concept grounded in the openEHR Reference Model (RM). It defines how data is structured, constrained, and identified.

- Each archetype represents **one coherent clinical or domain concept**
- Modelled independent of UIs or workflows

---

## Two-Level Modelling and Separation of Concerns

openEHR separates stable **Reference Model (RM)** from expressive **archetypes**. 
Archetypes expose domain semantics; the RM provides stable data structures. This enables independent evolution of content and systems.

---

## Terminology Neutrality

Archetypes are terminology-neutral; external code systems (SNOMED CT, LOINC) can be bound but are not mandatory. Bindings reflect clinical semantics, not implementation convenience.

---

## Unique Identification and Semantic Paths

Each archetype element has a unique path enabling unambiguous data reference and AQL querying.

---

## Backwards-Compatible Evolution

Changes must preserve data validity where possible. Incompatible changes require major version increments.

---

## Reuse and Specialisation

Maximise reuse of existing archetypes. Specialise only for true semantic subtypes, not convenience. Maintain clear lineage to parent artefacts.

---

## Archetypes Model Data, Not Process

Archetypes describe what data means, not when or how it is collected. Workflow and UI constraints belong in templates or applications.

---

## Templates Are Not Archetypes

Templates aggregate archetypes for specific use cases. If a model is scenario-specific, it belongs in a template, not an archetype.

---

## Governance and Clinical Validation

Archetypes require multidisciplinary review, clear documentation, and transparent governance to ensure interoperability.

---

## Clarity and Usability

Archetypes must have clear metadata (purpose, definitions, usage) understandable by clinicians and implementers.

---
