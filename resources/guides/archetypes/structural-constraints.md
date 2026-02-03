# openEHR Archetype Structural Constraint Guide

**Purpose:** Guidance on archetype constraints for clinically meaningful structure
**Keywords:** cardinality, existence, occurrences, slots, constraints

---

## Design Philosophy

> Constrain only what is universally true.

Archetypes should be clinically safe, maximally reusable, and free of local workflow assumptions.

---

## Existence

**Existence** (AOM 1.4) constrains `C_ATTRIBUTE` — whether an attribute value must be present.

- **Mandatory (`1..1`):** Use only when intrinsic to the concept; absence invalidates the record
- **Optional (`0..1`):** Default for contextual qualifiers and conditional data

**Note:** Existence applies to attributes. For object-level optionality, use **occurrences**.

---

## Cardinality and Occurrences

- **Single:** when the real-world concept is singular
- **Multiple:** only when repetition is clinically meaningful

**Avoid:** `0..*` as default; arbitrary upper bounds without rationale.

Upper bounds should reflect real-world constraints and be clinically justified.

---

## Slots

Use slots when:
- Content varies by context
- Multiple domain-specific implementations exist
- Reuse across specialisations is expected

**Constraints:**
- Constrain by archetype type and purpose
- Avoid unconstrained slots
- Document intended usage

---

## Clusters vs Elements

- **CLUSTER:** logically grouped sub-concepts
- **ELEMENT:** atomic data values
- Do not use clusters as generic containers

---

## Avoiding Over-Constraint

Do not encode in archetypes:
- UI layout
- Workflow sequencing
- Local business rules
- Template-level decisions

These belong in **templates**.

---

## Anti-Patterns

- Making everything mandatory
- Excessive nesting without semantic value
- Deep hierarchies compensating for poor scoping
- Slots bypassing modelling decisions

---

## Review Questions

- Could this constraint prevent legitimate reuse?
- Is this universally true?
- Should this be a template concern?

---
