{**
 * 2007-2022 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="panel">
    <h3><i class="icon icon-sitemap"></i> {l s='Widget Support' d='Modules.Sezzle.Admin'}</h3>
    <p>{l s='Submit a request to get help on your widget configuration. Our team will work on quickly resolving the issue.' d='Modules.Sezzle.Admin'}</p>
    <form action="{$sezzle_widget_queue_form_action|escape:'htmlall':'UTF-8'}" method="post">
        <button type="submit" class="btn btn-primary" id="widget_queue"
                {if not $sezzle_can_add_to_widget_queue }
                    disabled
                    style="background-color: #A3A3A3"
                {/if}
                name="submitWidgetQueueRequest">{l s='Request' d='Modules.Sezzle.Admin'}</button>
    </form>
</div>
