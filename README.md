# [Report](poggit.pmmp.io/p/Report) [![](https://poggit.pmmp.io/shield.state/Report)](https://poggit.pmmp.io/p/Report)

## Features
- **Accept/Decline** Report
- **Custom** Messages & Reasons
- **MySQL** Support
- **Webhook** Support
- **Notifications**
- **Saving** reports for later

## Commands
| Usage    | Description          | Permission             |
|----------|----------------------|------------------------|
| /report  | Report a player      | No Permission          |
| /reports | See all open reports | report.command.reports |

## Permissions
| Permission    | Use                      |
|---------------|--------------------------|
| report.notify | Getting notifications    |

## Configuration
```yaml
provider: mysql
mysql:
  host: localhost
  port: 3306
  username: root
  password: your_password
  database: your_database
webhook:
  enabled: false
  webhook_url: your_url
reasons:
  behavior:
    command: /mute %user% Behavior
    display_name: §cBehavior
  spamming:
    command: /mute %user% Spamming
    display_name: §cSpamming
  cheating:
    command: /ban %user% Cheating
    display_name: §cCheating
```

## Messages
```yaml
prefix: "§cReport §8» §r"
no.permissions: "{PREFIX}§cYou don't have the permission to do this!"
command.report.description: "Report a player"
command.reports.description: "See the open reports"
join.notification: "{PREFIX}§7There are §e%count% report(s) §7available!"
no.player.online: "{PREFIX}§cThere are no players online!"
player.not.found: "{PREFIX}§cThe player §e%target% §cwas not found!"
reason.not.found: "{PREFIX}§cThe reason §e%reason% §cdoesn't exists!"
player.already.reported: "{PREFIX}§cThe player §e%target% §cgot already reported!"
player.report.failed: "{PREFIX}§cYou can't report yourself!"
player.reported: "{PREFIX}§7You have successfully §areported §7the player §e%target%§7!"
no.reports.available: "{PREFIX}§cThere are no open reports!"
notify.message: "{PREFIX}§7The player §e%player% §7has reported §e%target% §7for §c§l%reason%§r§7!"
report.accepted: "{PREFIX}§7Your report against §e%target% §7was §aaccepted§7!"
report.accepted.with_notes: "{PREFIX}§7Your report against §e%target% §7was §aaccepted§7!\n{PREFIX}§7Notes: §e%notes%"
report.declined: "{PREFIX}§7Your report against §e%target% §7was §cdeclined§7!"
report.declined.with_notes: "{PREFIX}§7Your report against §e%target% §7was §cdeclined§7!\n{PREFIX}§7Notes: §e%notes%"
self.report.accepted: "{PREFIX}§7You have §aaccepted §7the report against §e%target% §7fom §e%player%§7!"
self.report.declined: "{PREFIX}§7You have §cdeclined §7the report against §e%target% §7fom §e%player%§7!"
report.not.found: "{PREFIX}§cThe report doesn't exists!"
form.report.title: "§8» §cReport §8«"
form.report.player.text: "§7Who do you want to report?"
form.report.reason.text: "§7For what reason?"
form.reports.title: "§8» §cReports §8«"
form.reports.text: "§7There are §e%count% report(s) §7available!"
form.reports.button.format: "§e%target%\n§c%reason%"
form.view_report.title: "§8» §e%target% §8«"
form.view_report.text: "§8» §7Target: §e%target%\n§8» §7Player: §e%player%\n§8» §7Reason: §e%reason%"
form.view_report.button.accept: "§aAccept"
form.view_report.button.decline: "§cDecline"
form.additional_notes.title: "§8» §6Notes §8«"
form.additional_notes.input.text: "§7Want to add some notes?"

webhook.new.embed.title: "New Report!"
webhook.new.field.target.name: "Target"
webhook.new.field.target.value: "%target%"
webhook.new.field.player.name: "Reported by"
webhook.new.field.player.value: "%player%"
webhook.new.field.reason.name: "Reason"
webhook.new.field.reason.value: "%reason%"

webhook.accepted.embed.title: "Report was accepted"
webhook.accepted.field.target.name: "Target"
webhook.accepted.field.target.value: "%target%"
webhook.accepted.field.player.name: "Player"
webhook.accepted.field.player.value: "%player%"
webhook.accepted.field.moderator.name: "Moderator"
webhook.accepted.field.moderator.value: "%moderator%"
webhook.accepted.field.notes.name: "Notes"
webhook.accepted.field.notes.value: "%notes%"
webhook.accepted.field.notes.value.empty: "No notes given"

webhook.declined.embed.title: "Report was declined"
webhook.declined.field.target.name: "Target"
webhook.declined.field.target.value: "%target%"
webhook.declined.field.player.name: "Player"
webhook.declined.field.player.value: "%player%"
webhook.declined.field.moderator.name: "Moderator"
webhook.declined.field.moderator.value: "%moderator%"
webhook.declined.field.notes.name: "Notes"
webhook.declined.field.notes.value: "%notes%"
webhook.declined.field.notes.value.empty: "No notes given"
```
