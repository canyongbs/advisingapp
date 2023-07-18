<nav
    class="relative z-10 flex flex-wrap items-center justify-between bg-white px-6 py-4 shadow-xl md:fixed md:bottom-0 md:left-0 md:top-0 md:block md:w-64 md:flex-row md:flex-nowrap md:overflow-hidden md:overflow-y-auto">
    <div
        class="mx-auto flex w-full flex-wrap items-center justify-between px-0 md:min-h-full md:flex-col md:flex-nowrap md:items-stretch">
        <button
            class="cursor-pointer rounded border border-solid border-transparent bg-transparent px-3 py-1 text-xl leading-none text-black opacity-50 md:hidden"
            type="button"
            onclick="toggleNavbar('example-collapse-sidebar')"
        >
            <i class="fas fa-bars"></i>
        </button>
        <a
            class="mr-0 inline-block whitespace-nowrap p-4 px-0 text-left text-sm font-bold uppercase text-blueGray-700 md:block md:pb-2"
            href="{{ route('admin.home') }}"
        >
            {{ trans('panel.site_title') }}
        </a>
        <div
            class="absolute left-0 right-0 top-0 z-40 hidden h-auto flex-1 items-center overflow-y-auto overflow-x-hidden rounded shadow md:relative md:mt-4 md:flex md:flex-col md:items-stretch md:opacity-100 md:shadow-none"
            id="example-collapse-sidebar"
        >
            <div class="mb-4 block border-b border-solid border-blueGray-300 pb-4 md:hidden md:min-w-full">
                <div class="flex flex-wrap">
                    <div class="w-6/12">
                        <a
                            class="mr-0 inline-block whitespace-nowrap p-4 px-0 text-left text-sm font-bold uppercase text-blueGray-700 md:block md:pb-2"
                            href="{{ route('admin.home') }}"
                        >
                            {{ trans('panel.site_title') }}
                        </a>
                    </div>
                    <div class="flex w-6/12 justify-end">
                        <button
                            class="cursor-pointer rounded border border-solid border-transparent bg-transparent px-3 py-1 text-xl leading-none text-black opacity-50 md:hidden"
                            type="button"
                            onclick="toggleNavbar('example-collapse-sidebar')"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <form class="mb-4 mt-6 md:hidden">
                <div class="mb-3 pt-0">
                    @livewire('global-search')
                </div>
            </form>

            <!-- Divider -->
            <div class="flex md:hidden">
                @if (file_exists(app_path('Http/Livewire/LanguageSwitcher.php')))
                    <livewire:language-switcher />
                @endif
            </div>
            <hr class="mb-6 md:min-w-full" />
            <!-- Heading -->

            <ul class="flex list-none flex-col md:min-w-full md:flex-col">
                <li class="items-center">
                    <a
                        class="{{ request()->is('admin') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                        href="{{ route('admin.home') }}"
                    >
                        <i class="fas fa-tv"></i>
                        {{ trans('global.dashboard') }}
                    </a>
                </li>

                @can('record_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/record-student-items*') || request()->is('admin/engagement-student-files*') || request()->is('admin/record-enrollment-items*') || request()->is('admin/record-program-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon far fa-folder-open">
                            </i>
                            {{ trans('cruds.recordMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('record_student_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/record-student-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.record-student-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-user-graduate">
                                        </i>
                                        {{ trans('cruds.recordStudentItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_student_file_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-student-files*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-student-files.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-file-alt">
                                        </i>
                                        {{ trans('cruds.engagementStudentFile.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('record_enrollment_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/record-enrollment-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.record-enrollment-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-database">
                                        </i>
                                        {{ trans('cruds.recordEnrollmentItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('record_program_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/record-program-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.record-program-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-book-open">
                                        </i>
                                        {{ trans('cruds.recordProgramItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('prospect_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/prospect-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon far fa-address-book">
                            </i>
                            {{ trans('cruds.prospectMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('prospect_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/prospect-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.prospect-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-user">
                                        </i>
                                        {{ trans('cruds.prospectItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('engage_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/engagement-interaction-items*') || request()->is('admin/engagement-email-items*') || request()->is('admin/engagement-text-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-handshake">
                            </i>
                            {{ trans('cruds.engageMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('engagement_interaction_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-interaction-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-interaction-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-headset">
                                        </i>
                                        {{ trans('cruds.engagementInteractionItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_email_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-email-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-email-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-envelope">
                                        </i>
                                        {{ trans('cruds.engagementEmailItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_text_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-text-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-text-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-comments">
                                        </i>
                                        {{ trans('cruds.engagementTextItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('kb_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/kb-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-book">
                            </i>
                            {{ trans('cruds.kbMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('kb_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/kb-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.kb-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-file-alt">
                                        </i>
                                        {{ trans('cruds.kbItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('case_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/case-items*') || request()->is('admin/case-update-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-briefcase">
                            </i>
                            {{ trans('cruds.caseMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('case_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/case-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.case-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-folder-open">
                                        </i>
                                        {{ trans('cruds.caseItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('case_update_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/case-update-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.case-update-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-volume-up">
                                        </i>
                                        {{ trans('cruds.caseUpdateItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('journey_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/journey-email-items*') || request()->is('admin/journey-text-items*') || request()->is('admin/journey-target-lists*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-drafting-compass">
                            </i>
                            {{ trans('cruds.journeyMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('journey_email_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/journey-email-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.journey-email-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-at">
                                        </i>
                                        {{ trans('cruds.journeyEmailItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('journey_text_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/journey-text-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.journey-text-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-comment-alt">
                                        </i>
                                        {{ trans('cruds.journeyTextItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('journey_target_list_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/journey-target-lists*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.journey-target-lists.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-database">
                                        </i>
                                        {{ trans('cruds.journeyTargetList.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('support_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/support-items*') || request()->is('admin/support-training-items*') || request()->is('admin/support-feedback-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon far fa-life-ring">
                            </i>
                            {{ trans('cruds.supportMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('support_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/support-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.support-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-ambulance">
                                        </i>
                                        {{ trans('cruds.supportItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('support_training_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/support-training-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.support-training-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-chalkboard">
                                        </i>
                                        {{ trans('cruds.supportTrainingItem.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('support_feedback_item_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/support-feedback-items*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.support-feedback-items.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-bullhorn">
                                        </i>
                                        {{ trans('cruds.supportFeedbackItem.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('report_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/report-students*') || request()->is('admin/report-prospects*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-chart-pie">
                            </i>
                            {{ trans('cruds.reportMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('report_student_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/report-students*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.report-students.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-graduation-cap">
                                        </i>
                                        {{ trans('cruds.reportStudent.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('report_prospect_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/report-prospects*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.report-prospects.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.reportProspect.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('settings_menu_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/institutions*') || request()->is('admin/case-item-statuses*') || request()->is('admin/case-item-types*') || request()->is('admin/case-item-priorities*') || request()->is('admin/kb-item-qualities*') || request()->is('admin/kb-item-statuses*') || request()->is('admin/kb-item-categories*') || request()->is('admin/engagement-interaction-types*') || request()->is('admin/engagement-interaction-drivers*') || request()->is('admin/engagement-interaction-outcomes*') || request()->is('admin/engagement-interaction-relations*') || request()->is('admin/support-pages*') || request()->is('admin/prospect-statuses*') || request()->is('admin/prospect-sources*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-cogs">
                            </i>
                            {{ trans('cruds.settingsMenu.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('institution_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/institutions*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.institutions.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-school">
                                        </i>
                                        {{ trans('cruds.institution.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('case_item_status_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/case-item-statuses*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.case-item-statuses.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-folder-open">
                                        </i>
                                        {{ trans('cruds.caseItemStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('case_item_type_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/case-item-types*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.case-item-types.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-folder-open">
                                        </i>
                                        {{ trans('cruds.caseItemType.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('case_item_priority_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/case-item-priorities*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.case-item-priorities.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-folder-open">
                                        </i>
                                        {{ trans('cruds.caseItemPriority.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('kb_item_quality_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/kb-item-qualities*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.kb-item-qualities.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-file-alt">
                                        </i>
                                        {{ trans('cruds.kbItemQuality.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('kb_item_status_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/kb-item-statuses*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.kb-item-statuses.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-file-alt">
                                        </i>
                                        {{ trans('cruds.kbItemStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('kb_item_category_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/kb-item-categories*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.kb-item-categories.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-file-alt">
                                        </i>
                                        {{ trans('cruds.kbItemCategory.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_interaction_type_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-interaction-types*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-interaction-types.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-headset">
                                        </i>
                                        {{ trans('cruds.engagementInteractionType.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_interaction_driver_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-interaction-drivers*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-interaction-drivers.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-headset">
                                        </i>
                                        {{ trans('cruds.engagementInteractionDriver.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_interaction_outcome_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-interaction-outcomes*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-interaction-outcomes.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-headset">
                                        </i>
                                        {{ trans('cruds.engagementInteractionOutcome.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('engagement_interaction_relation_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/engagement-interaction-relations*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.engagement-interaction-relations.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-headset">
                                        </i>
                                        {{ trans('cruds.engagementInteractionRelation.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('support_page_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/support-pages*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.support-pages.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-life-ring">
                                        </i>
                                        {{ trans('cruds.supportPage.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('prospect_status_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/prospect-statuses*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.prospect-statuses.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-user">
                                        </i>
                                        {{ trans('cruds.prospectStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('prospect_source_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/prospect-sources*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.prospect-sources.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon far fa-user">
                                        </i>
                                        {{ trans('cruds.prospectSource.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="items-center">
                        <a
                            class="has-sub {{ request()->is('admin/users*') || request()->is('admin/permissions*') || request()->is('admin/roles*') || request()->is('admin/user-alerts*') || request()->is('admin/audit-logs*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                            href="#"
                            onclick="window.openSubNav(this)"
                        >
                            <i class="fa-fw fas c-sidebar-nav-icon fa-users">
                            </i>
                            {{ trans('cruds.userManagement.title') }}
                        </a>
                        <ul class="subnav ml-4 hidden">
                            @can('user_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/users*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.users.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-user">
                                        </i>
                                        {{ trans('cruds.user.title') }}
                                    </a>
                                </li>
                            @endcan
{{--                            @can('permission_access')--}}
{{--                                <li class="items-center">--}}
{{--                                    <a--}}
{{--                                        class="{{ request()->is('admin/permissions*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"--}}
{{--                                        href="{{ route('admin.permissions.index') }}"--}}
{{--                                    >--}}
{{--                                        <i class="fa-fw c-sidebar-nav-icon fas fa-unlock-alt">--}}
{{--                                        </i>--}}
{{--                                        {{ trans('cruds.permission.title') }}--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
{{--                            @can('role_access')--}}
{{--                                <li class="items-center">--}}
{{--                                    <a--}}
{{--                                        class="{{ request()->is('admin/roles*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"--}}
{{--                                        href="{{ route('admin.roles.index') }}"--}}
{{--                                    >--}}
{{--                                        <i class="fa-fw c-sidebar-nav-icon fas fa-briefcase">--}}
{{--                                        </i>--}}
{{--                                        {{ trans('cruds.role.title') }}--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
                            @can('user_alert_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/user-alerts*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.user-alerts.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-bell">
                                        </i>
                                        {{ trans('cruds.userAlert.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('audit_log_access')
                                <li class="items-center">
                                    <a
                                        class="{{ request()->is('admin/audit-logs*') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                        href="{{ route('admin.audit-logs.index') }}"
                                    >
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-file-alt">
                                        </i>
                                        {{ trans('cruds.auditLog.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @if (file_exists(app_path('Http/Controllers/Auth/UserProfileController.php')))
                    @can('auth_profile_edit')
                        <li class="items-center">
                            <a
                                class="{{ request()->is('profile') ? 'sidebar-nav-active' : 'sidebar-nav' }}"
                                href="{{ route('profile.show') }}"
                            >
                                <i class="fa-fw c-sidebar-nav-icon fas fa-user-circle"></i>
                                {{ trans('global.my_profile') }}
                            </a>
                        </li>
                    @endcan
                @endif

                <li class="items-center">
                    <a
                        class="sidebar-nav"
                        href="#"
                        onclick="event.preventDefault(); document.getElementById('logoutform').submit();"
                    >
                        <i class="fa-fw fas fa-sign-out-alt"></i>
                        {{ trans('global.logout') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
