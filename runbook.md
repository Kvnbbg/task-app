# Runbook
## Deployment Failed
- Diagnostic: railway logs
- Causes: 1) Wrong $PORT. 2) Missing env.
- Mitigation: Verify start command.
- Rollback: railway rollback previous
