# Minimal ADL Idiom Cheat Sheet

**Purpose:** Fast grounding for writing/reviewing ADL constraint trees without changing semantics
**Related:** Derived from `openehr://guides/archetypes/adl-syntax`; ties go to adl-syntax.
**Keywords:** ADL, constraint, syntax, idioms, cheat sheet, minimal, fast, QA

---

## Mental model
- Archetype = constraints on **RM objects** and their **attributes**
- Tree = **C_OBJECT** (objects) + **C_ATTRIBUTE** (attributes)
- Leaf value types are **DV_\*** and usually need at least light constraints

---

## Root and type correctness
**Idiom:** root node matches the declared RM type.
- If archetype is an OBSERVATION, root constraint must be OBSERVATION (not CLUSTER/ELEMENT).

---

## Constrain attributes by “matches { … }”
**Idiom:** every constrained attribute is expressed as:
`<rm_attribute> matches { <constraint> }`

Avoid inventing attribute names. Use the RM attribute name exactly.

---

## Occurrences vs Cardinality
- **occurrences** = how many times an *object node* may appear
- **cardinality** = how many children an *attribute container* may hold

**Idiom:**
- Use `occurrences` on repeated child objects
- Use `cardinality` on multi-valued attributes (containers)

---

## “Existence” as the default optionality lever
**Idiom:** prefer making nodes optional/mandatory via existence/occurrences rather than overfitting with complex structures.

- Optional: `0..1`
- Mandatory: `1..1`

---

## DV_TEXT / DV_CODED_TEXT / DV_QUANTITY: canonical leaf patterns

### Free text leaf
Use DV_TEXT for narrative.
**Idiom:** keep it lightly constrained unless universal limits exist.

### Coded leaf
Use DV_CODED_TEXT when values come from a value set.
**Idiom:** define a value set and bind it (don’t rely on free text).

### Quantity leaf
Use DV_QUANTITY when you have magnitude + units.
**Idiom:** constrain units and/or magnitude where clinically universal.

---

## Value sets: use ac-codes consistently
**Idiom:**
- Define a value set as an `acNNNN` group
- Reference that set from coded nodes

---

## Paths must stay stable
**Idiom:** don’t restructure the tree just for aesthetics.
- Stable paths are more important than “pretty structure”
- Refactoring that changes paths usually implies a major version bump

---

## Slots: constrain intent, don’t wildcard
**Idiom:** slots should be constrained to a known archetype family/type.

Avoid unconstrained slots unless you truly need “any CLUSTER”.

---

## Protocol vs data vs state: keep semantics clean
**Idiom:**
- `data` = what was observed/recorded
- `protocol` = how it was measured/recorded (method, device, position)
- `state` = relevant state at the time (where applicable)

Don’t encode workflow sequencing.

---

## “Don’t change meaning” rule for syntax fixes
When asked to “fix ADL”, default to:
- Preserve concept scope
- Preserve paths
- Preserve value semantics
  Only fix structural/formal issues.

---

## Micro check before calling it “done”
- Parses?
- All coded nodes have term definitions?
- No invented RM attributes?
- Occurrences/cardinality used correctly?
- Slots constrained?

---