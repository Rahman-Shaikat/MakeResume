# Resume template naming

The image filenames in this directory are source references and remain unchanged.
Application templates use one sequential naming convention across catalog slugs,
Blade files, partials, CSS namespaces, and tests.

| Source reference | Application slug | Blade template |
| --- | --- | --- |
| Original application design | `template-one` | `template-one.blade.php` |
| `temp-1.png` | `template-two` | `template-two.blade.php` |
| `temp-2.png` | `template-three` | `template-three.blade.php` |
| `temp-3.png` | `template-four` | `template-four.blade.php` |

Future implementations should continue with `template-five`, `template-six`, and
so on. Related partials and CSS selectors should use the same number word, such
as `template-five-main.blade.php` and `.resume-template-five`.
