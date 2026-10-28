# Project instructions

- Preserve the existing Laravel development server during project work. Do not stop, restart, replace, or attach to its console process unless the user explicitly asks.
- Never run `composer run dev` for routine checks or changes. Its multi-process launcher can make web availability depend on unrelated helpers. Run one-shot Artisan checks separately from the server.
- If the user asks to start a stopped local server, launch it as a hidden detached process and verify its existing port; do not open a foreground CMD window or start a second server on the same port.
- Before finishing any task that runs project commands, confirm the original server remains available.