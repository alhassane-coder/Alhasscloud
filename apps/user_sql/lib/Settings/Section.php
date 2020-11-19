<?php
/**
 * Nextcloud - user_sql
 *
 * @copyright 2018 Marcin Łojewski <dev@mlojewski.me>
 * @author    Marcin Łojewski <dev@mlojewski.me>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace OCA\UserSQL\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

/**
 * The section item.
 *
 * @author Marcin Łojewski <dev@mlojewski.me>
 */
class Section implements IIconSection
{
    /**
     * @var string The application name.
     */
    private $appName;
    /**
     * @var IURLGenerator The URL generator.
     */
    private $urlGenerator;
    /**
     * @var IL10N The localization service.
     */
    private $localization;

    /**
     * The class constructor.
     *
     * @param string        $AppName      The application name.
     * @param IURLGenerator $urlGenerator The URL generator.
     * @param IL10N         $localization The localization service.
     */
    public function __construct(
        $AppName, IURLGenerator $urlGenerator, IL10N $localization
    ) {
        $this->appName = $AppName;
        $this->urlGenerator = $urlGenerator;
        $this->localization = $localization;
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return $this->appName;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->localization->t("SQL Backends");
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 25;
    }

    /**
     * @inheritdoc
     */
    public function getIcon()
    {
        return $this->urlGenerator->imagePath($this->appName, "app-dark.svg");
    }
}
