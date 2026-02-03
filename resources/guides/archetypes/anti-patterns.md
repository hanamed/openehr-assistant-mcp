# openEHR Archetype Anti-Patterns

**Purpose:** Common modelling pitfalls to avoid
**Keywords:** archetype, anti-patterns, modelling, pitfalls, avoid, best practice

---

## Overly Broad Concepts

**Problem:** Combining multiple unrelated clinical domains in one archetype  
**Example:** Mixing vital signs, assessment, and medication.  
**Impact:** Poor reuse, inconsistent RM alignment, harder querying.  
**Fix:** Create separate archetypes; use slots for composition.

---

## Undocumented Terminology Bindings

**Problem:** Code bindings without specifying authority or meaning  
**Example:** Arbitrary codes without SNOMED CT/LOINC reference.  
**Impact:** Loss of interoperability, ambiguity.  
**Fix:** Always reference authoritative terminology bindings.

---

## Excessive Specialisation

**Problem:** Many specialisations for minor local variations  
**Example:** Specialising "serum glucose" for non-clinical uses.  
**Impact:** Fragmentation, reduced cross-site sharing.  
**Fix:** Use templates instead of specialisations.

---

## Ignoring RM Semantics

**Problem:** Modelling that conflicts with RM intent  
**Example:** Using OBSERVATION as generic record.  
**Impact:** Data inconsistencies, runtime errors.  
**Fix:** Use appropriate RM types and attributes.

---

## Hardcoding Workflow Logic

**Problem:** Embedding workflow or UI constraints in archetypes  
**Example:** Encoding process ordering as structural constraints.  
**Impact:** Reduced portability; conflicts with template-level specificity.  
**Fix:** Keep archetypes for data semantics only.

---

## Improper Cardinality Constraints

**Problem:** Arbitrary min/max values without clinical justification  
**Example:** Mandatory multiple occurrences where single suffices.  
**Impact:** Erroneous data capture expectations.  
**Fix:** Justify and document all cardinality constraints.

---

## Path-Breaking Refactors

**Problem:** Structural changes that alter archetype paths  
**Example:** Moving nodes for readability in a minor version.  
**Impact:** Query breakage, data migration issues.  
**Fix:** Treat paths as public API; path changes require major version.

---
