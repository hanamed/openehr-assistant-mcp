# openEHR Archetype Design & Review Checklist

**Purpose:** Quality checklist for archetype design, review, and publication
**Keywords:** checklist, QA, design, review, quality, consistency, best practice, assessment, guideline

---

## Key Principles

1. **Single Coherent Concept**
   - Ensure archetype represents one clinical concept, universally usable. 
   - The scope should be universal, usable across diverse care settings.
2. **Stable Definitions**
   - Clear names and descriptions reflecting clinical intent.
   - Archetypes should aim for *wide reuse* where sensible, avoiding unnecessary fragmentation.

---

## Header & Metadata

- [ ] Archetype ID follows conventions (namespace, versioning)
- [ ] Original language set (ISO-639-1)
- [ ] Purpose and usage fields complete
- [ ] Author, contributor, licensing metadata present

---

## Definition & Structure

- [ ] Root node matches targeted clinical concept
- [ ] Cardinality constraints justified
- [ ] Appropriate RM types (OBSERVATION, EVALUATION, etc.)
- [ ] Logical C_OBJECT/C_ATTRIBUTE hierarchy following RM semantics
- [ ] Correct use of existence (attributes), occurrences (objects), cardinality (containers)
- [ ] Internal references (`use_node`) for repeated structures

---

## Terminology

- [ ] Coded values bind to recognised systems (SNOMED CT, LOINC, ICD)
- [ ] Terms match clinical concept semantics
- [ ] Unambiguous term definitions

---

## Semantic Clarity

- [ ] Definitions reflect clinical meaning, not implementation
- [ ] Data semantics independent of workflow/UI
- [ ] Required vs optional elements justified

---

## Translation

- [ ] Translations preserve clinical intent
- [ ] Natural target-language phrasing
- [ ] Consistent terminology throughout
- [ ] No changes to identifiers or structure
- [ ] Aligned with authoritative local terminology

---

## Editorial Review

- [ ] Name accurately reflects content
- [ ] Single concept scope (not too narrow/broad)
- [ ] Protocol/State sections used correctly
- [ ] Metadata complete and translated
- [ ] Internal term and cardinality consistency
- [ ] No duplicate content (refactor to clusters)

---

## Reuse & Specialisation

- [ ] Existing archetypes reviewed before creating new
- [ ] Consistent with related domain archetypes
- [ ] Slots reference appropriate archetypes
- [ ] Specialisations justify divergence from parent


---

## Paths & Identifiers

- [ ] All at-codes defined in `term_definitions`
- [ ] All ac-codes defined in `constraint_definitions`
- [ ] Identifiers unchanged from compatible versions
- [ ] Paths stable for long-term AQL use
- [ ] ADL 1.4 validity rules pass (VARID, VARCN, VARDF, VARON, VARDT, VATDF, VACDF)

---

## Versioning

- [ ] Version increment reflects semantic impact
- [ ] Backward compatibility assessed
- [ ] Deprecated elements retained and marked
- [ ] Revision history documents changes

---

## Documentation

- [ ] Example instances or use-case sketches provided
- [ ] Rationale for non-obvious choices documented
- [ ] Links to related templates/use cases
- [ ] Known limitations documented

---
