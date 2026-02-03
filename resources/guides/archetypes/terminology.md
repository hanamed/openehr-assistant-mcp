# openEHR Archetype Terminology & Ontology Guide

**Purpose:** Provide guidance on terminology modelling and terminology binding in archetypes, including value sets and external terminology bindings.
**Keywords:** terminology, archetypes, value sets, external bindings, term, binding, ontology, code

---

## Core Principle: Archetypes are Terminology-Neutral

Archetypes define **clinical meaning**, not terminologies.

- Terminology bindings are **optional but recommended**
- Archetypes must remain usable even if bindings are absent
- External code systems must *not* replace clear internal definitions

---

## Internal vs External Terminology

### Internal Terminology (Archetype Terms)

Each coded node must have:
- A clear **text**
- A precise **definition**
- Stable semantic meaning across versions

**Rule:**
> Internal term definitions are authoritative; external codes are references.

---

### External Terminology Bindings

Bindings may reference:
- openEHR
- SNOMED CT
- LOINC
- ICD
- Other recognised ontologies

**Rules:**
- Bindings must match the **exact semantic intent** of the node
- Do not bind to overly generic or loosely related concepts
- Do not mix code systems within a single value set unless justified
- openEHR terminology binding should be valid against openEHR terminology (accessible via `openehr://terminology`)

---

## Value Sets and DV_CODED_TEXT

### Use Coded Value Sets When:
- The domain concept is clinically enumerated
- Comparability or analytics is expected
- International reuse is anticipated

### Avoid Coded Value Sets When:
- The domain is free-text by nature
- Values are unpredictable or narrative

---

## Binding Granularity

- Bind **leaf nodes**, not structural containers
- Avoid binding at multiple hierarchy levels for the same concept
- Do not bind implementation artefacts (e.g., protocol metadata)

---

## Language and Localisation

- **Principle: No Language Primacy.** Archetypes are fully translatable; they can be authored in any language (though English is preferred for international CKM submission).
- **Semantic Preservation:** Translations must preserve the exact clinical meaning and intent, not necessarily literal word-order.
- **Natural Phrasing:** Use the target language's clinical register; depart from awkward source wording to produce natural phrasing.
- **Consistency:** Maintain internal consistency in terminology and grammatical forms (e.g., definite/indefinite forms).
- **Prohibitions:** Do not translate archetype class names (e.g., ACTION, OBSERVATION). Never change node identifiers (`at-codes`, `ac-codes`) or computable structure during translation.
- **Translate Metadata:** Narrative fields (Purpose, Use, Misuse, etc.).
- **Localisation:** Avoid encoding locale-specific semantics or business logic in term text; local presentation belongs to the UI/template layer.

---

## Ontological Alignment

Archetype concepts should align with:
- Real-world clinical ontology structure
- Established domain modelling patterns
- Existing CKM artefacts where applicable

> If no suitable ontology concept exists, document the gap explicitly.

---

## Common Terminology Anti-Patterns

- Binding vague nodes (e.g. “Other”, “Miscellaneous”)
- Reusing codes with different meanings
- Using local or proprietary codes without justification
- Encoding workflow states as coded clinical values

---
