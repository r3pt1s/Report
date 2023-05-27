<?php

namespace r3pt1s\reportsystem\form;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use r3pt1s\reportsystem\manager\Report;
use r3pt1s\reportsystem\manager\ReportManager;
use r3pt1s\reportsystem\util\Utils;

class ReportsForm extends MenuForm {

    public function __construct() {
        $options = array_map(fn(Report $report) => new MenuOption(Utils::parseMessage("form.reports.button.format", [
            "%target%" => $report->getTarget(),
            "%reason%" => $report->getReasonDisplay()
        ])), $reports = array_values(ReportManager::getInstance()->getReports()));
        parent::__construct(
            Utils::parseMessage("form.reports.title"),
            Utils::parseMessage("form.reports.text", [
                "%count%" => count(ReportManager::getInstance()->getReports())
            ]),
            $options,
            function(Player $player, int $data) use ($reports): void {
                if (isset($reports[$data]) && ($chosenReport = ReportManager::getInstance()->getReport($reports[$data]->getTarget())) !== null) {
                    $player->sendForm(new MenuForm(
                        Utils::parseMessage("form.view_report.title", [
                            "%target%" => $chosenReport->getTarget(),
                            "%reason%" => $chosenReport->getReasonDisplay()
                        ]),
                        Utils::parseMessage("form.view_report.text", [
                            "%target%" => $chosenReport->getTarget(),
                            "%reason%" => $chosenReport->getReasonDisplay(),
                            "%player%" => $chosenReport->getPlayer()
                        ]),
                        [
                            new MenuOption(Utils::parseMessage("form.view_report.button.accept")),
                            new MenuOption(Utils::parseMessage("form.view_report.button.decline"))
                        ],
                        function(Player $player, int $data) use($chosenReport): void {
                            $player->sendForm(new CustomForm(
                                Utils::parseMessage("form.additional_notes.title"),
                                [new Input("notes", Utils::parseMessage("form.additional_notes.input.text"))],
                                function(Player $player, CustomFormResponse $response) use($chosenReport, $data): void {
                                    if ($data == 0) {
                                        ReportManager::getInstance()->removeReport($chosenReport, $player, true, $response->getString("notes"));
                                        $player->sendMessage(Utils::parseMessage("self.report.accepted", [
                                            "%target%" => $chosenReport->getTarget(),
                                            "%player%" => $chosenReport->getPlayer()
                                        ]));
                                    } else {
                                        ReportManager::getInstance()->removeReport($chosenReport, $player, false, $response->getString("notes"));
                                        $player->sendMessage(Utils::parseMessage("self.report.declined", [
                                            "%target%" => $chosenReport->getTarget(),
                                            "%player%" => $chosenReport->getPlayer()
                                        ]));
                                    }
                                }
                            ));
                        }
                    ));
                } else {
                    $player->sendMessage(Utils::parseMessage("report.not.found"));
                }
            }
        );
    }
}