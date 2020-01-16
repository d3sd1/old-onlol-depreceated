<!-- BEGIN PAGE BREADCRUMB -->
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a ui-sref="dashboard">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li class="active" data-ng-bind="$state.current.data.pageTitle"> </li>
</ul>
<!-- END PAGE BREADCRUMB -->
<!-- BEGIN MAIN CONTENT -->
<div class="note note-success note-bordered">
    <h3>UI Bootstrap</h3>
    <p> Bootstrap components written in pure AngularJS by the AngularUI Team. For more info please check the
        <a href="http://angular-ui.github.io/bootstrap/">official site</a>
    </p>
</div>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN: ACCORDION DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Accordion</span>
                    <span class="caption-helper">ui.bootstrap.accordion</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="AccordionDemoCtrl">
                            <script type="text/ng-template" id="group-template.html">
                                <div class="panel {{panelClass || 'panel-default'}}">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="color:#fa39c3">
                                            <a href tabindex="0" class="accordion-toggle" ng-click="toggleOpen()" uib-accordion-transclude="heading">
                                                <span uib-accordion-header ng-class="{'text-muted': isDisabled}"> {{heading}} </span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" uib-collapse="!isOpen">
                                        <div class="panel-body" style="text-align: right" ng-transclude></div>
                                    </div>
                                </div>
                            </script>
                            <p>
                                <button type="button" class="btn btn-default btn-sm" ng-click="status.open = !status.open">Toggle last panel</button>
                                <button type="button" class="btn btn-default btn-sm" ng-click="status.isFirstDisabled = ! status.isFirstDisabled">Enable / Disable first panel</button>
                            </p>
                            <label class="mt-checkbox"> Open only one at a time
                                <input type="checkbox" ng-model="oneAtATime">
                                <span></span>
                            </label>
                            <uib-accordion close-others="oneAtATime">
                                <uib-accordion-group heading="Static Header, initially expanded" is-open="status.isFirstOpen" is-disabled="status.isFirstDisabled"> This content is straight in the template. </uib-accordion-group>
                                <uib-accordion-group heading="{{group.title}}" ng-repeat="group in groups"> {{group.content}} </uib-accordion-group>
                                <uib-accordion-group heading="Dynamic Body Content">
                                    <p>The body of the uib-accordion group grows to fit the contents</p>
                                    <button type="button" class="btn btn-default btn-sm" ng-click="addItem()">Add Item</button>
                                    <div ng-repeat="item in items">{{item}}</div>
                                </uib-accordion-group>
                                <uib-accordion-group heading="Custom template" template-url="group-template.html"> Hello </uib-accordion-group>
                                <uib-accordion-group is-open="status.isCustomHeaderOpen" template-url="group-template.html">
                                    <uib-accordion-heading> Custom template with custom header template
                                        <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.isCustomHeaderOpen, 'glyphicon-chevron-right': !status.isCustomHeaderOpen}"></i>
                                    </uib-accordion-heading> World </uib-accordion-group>
                                <uib-accordion-group heading="Delete account" panel-class="panel-danger">
                                    <p>Please, to delete your account, click the button below</p>
                                    <button class="btn btn-danger">Delete</button>
                                </uib-accordion-group>
                                <uib-accordion-group is-open="status.open">
                                    <uib-accordion-heading> I can have markup, too!
                                        <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.open, 'glyphicon-chevron-right': !status.open}"></i>
                                    </uib-accordion-heading> This is just some content to illustrate fancy headings. </uib-accordion-group>
                            </uib-accordion>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>The
                            <strong>accordion directive</strong> builds on top of the collapse directive to provide a list of items, with collapsible bodies that are collapsed or expanded by clicking on the item's header. </p>
                        <p>We can control whether expanding an item will cause the other items to close, using the <code>close-others</code> attribute on accordion.</p>
                        <p>The body of each accordion group is transcluded in to the body of the collapsible element.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: ACCORDION DEMO -->
        <!-- BEGIN ALERT DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Alert</span>
                    <span class="caption-helper">ui.bootstrap.alert</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="AlertDemoCtrl">
                            <script type="text/ng-template" id="alert.html">
                                <div class="alert" style="background-color:#fa39c3;color:white" role="alert">
                                    <div ng-transclude></div>
                                </div>
                            </script>
                            <uib-alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</uib-alert>
                            <uib-alert template-url="alert.html">A happy alert!</uib-alert>
                            <button type="button" class='btn btn-default' ng-click="addAlert()">Add Alert</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Alert is an AngularJS-version of bootstrap's alert.</p>
                        <p>This directive can be used to generate alerts from the dynamic model data (using the ng-repeat directive);</p>
                        <p>The presence of the "close" attribute determines if a close button is displayed</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END ALERT DEMO -->
        <!-- BEGIN BUTTONS DEMO -->
        <div class="portlet light bg-inverse">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Buttons</span>
                    <span class="caption-helper">ui.bootstrap.buttons</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="ButtonsCtrl">
                            <h4>Single toggle</h4> <pre>{{singleModel}}</pre>
                            <button type="button" class="btn btn-primary" ng-model="singleModel" uib-btn-checkbox btn-checkbox-true="1" btn-checkbox-false="0"> Single Toggle </button>
                            <h4>Checkbox</h4> <pre>Model: {{checkModel}}</pre> <pre>Results: {{checkResults}}</pre>
                            <div class="btn-group">
                                <label class="btn btn-primary" ng-model="checkModel.left" uib-btn-checkbox>Left</label>
                                <label class="btn btn-primary" ng-model="checkModel.middle" uib-btn-checkbox>Middle</label>
                                <label class="btn btn-primary" ng-model="checkModel.right" uib-btn-checkbox>Right</label>
                            </div>
                            <h4>Radio &amp; Uncheckable Radio</h4> <pre>{{radioModel || 'null'}}</pre>
                            <div class="btn-group">
                                <label class="btn btn-primary" ng-model="radioModel" uib-btn-radio="'Left'">Left</label>
                                <label class="btn btn-primary" ng-model="radioModel" uib-btn-radio="'Middle'">Middle</label>
                                <label class="btn btn-primary" ng-model="radioModel" uib-btn-radio="'Right'">Right</label>
                            </div>
                            <div class="btn-group">
                                <label class="btn btn-success" ng-model="radioModel" uib-btn-radio="'Left'" uncheckable>Left</label>
                                <label class="btn btn-success" ng-model="radioModel" uib-btn-radio="'Middle'" uncheckable>Middle</label>
                                <label class="btn btn-success" ng-model="radioModel" uib-btn-radio="'Right'" uib-uncheckable="uncheckable">Right</label>
                            </div>
                            <div>
                                <button class="btn btn-default" ng-click="uncheckable = !uncheckable"> Toggle uncheckable </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>There are two directives that can make a group of buttons behave like a set of checkboxes, radio buttons, or a hybrid where radio buttons can be unchecked.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END BUTTONS DEMO -->
        <!-- BEGIN TABS DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Tabs</span>
                    <span class="caption-helper">ui.bootstrap.tabs</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <style type="text/css">
                            form.tab-form-demo .tab-pane {
                                margin: 20px 20px;
                            }
                        </style>
                        <div ng-controller="TabsDemoCtrl">
                            <p>Select a tab by setting active binding to true:</p>
                            <p>
                                <button type="button" class="btn btn-default btn-sm" ng-click="active = 1">Select second tab</button>
                                <button type="button" class="btn btn-default btn-sm" ng-click="active = 2">Select third tab</button>
                            </p>
                            <p>
                                <button type="button" class="btn btn-default btn-sm" ng-click="tabs[1].disabled = ! tabs[1].disabled">Enable / Disable third tab</button>
                            </p>
                            <hr />
                            <uib-tabset active="active">
                                <uib-tab index="0" heading="Static title">Static content</uib-tab>
                                <uib-tab index="$index + 1" ng-repeat="tab in tabs" heading="{{tab.title}}" disable="tab.disabled"> {{tab.content}} </uib-tab>
                                <uib-tab index="3" select="alertMe()">
                                    <uib-tab-heading>
                                        <i class="glyphicon glyphicon-bell"></i> Alert! </uib-tab-heading> I've got an HTML heading, and a select callback. Pretty cool! </uib-tab>
                            </uib-tabset>
                            <hr />
                            <uib-tabset active="activePill" vertical="true" type="pills">
                                <uib-tab index="0" heading="Vertical 1">Vertical content 1</uib-tab>
                                <uib-tab index="1" heading="Vertical 2">Vertical content 2</uib-tab>
                            </uib-tabset>
                            <hr />
                            <uib-tabset active="activeJustified" justified="true">
                                <uib-tab index="0" heading="Justified">Justified content</uib-tab>
                                <uib-tab index="1" heading="SJ">Short Labeled Justified content</uib-tab>
                                <uib-tab index="2" heading="Long Justified">Long Labeled Justified content</uib-tab>
                            </uib-tabset>
                            <hr /> Tabbed pills with CSS classes
                            <uib-tabset type="pills">
                                <uib-tab heading="Default Size">Tab 1 content</uib-tab>
                                <uib-tab heading="Small Button" classes="btn-sm">Tab 2 content</uib-tab>
                            </uib-tabset>
                            <hr /> Tabs using nested forms:
                            <form name="outerForm" class="tab-form-demo">
                                <uib-tabset active="activeForm">
                                    <uib-tab index="0" heading="Form Tab">
                                        <ng-form name="nestedForm">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" required ng-model="model.name" /> </div>
                                        </ng-form>
                                    </uib-tab>
                                    <uib-tab index="1" heading="Tab One"> Some Tab Content </uib-tab>
                                    <uib-tab index="2" heading="Tab Two"> More Tab Content </uib-tab>
                                </uib-tabset>
                            </form> Model: <pre>{{ model | json }}</pre> Nested Form: <pre>{{ outerForm.nestedForm | json }}</pre> </div>
                    </div>
                    <div class="col-md-6">
                        <p>AngularJS version of the tabs directive.</p>
                        <h3>Settings</h3>
                        <h4><code>&lt;tabset&gt;</code></h4>
                        <ul>
                            <li>
                                <p><code>vertical</code>
                                    <em>(Defaults: false)</em> : Whether tabs appear vertically stacked. </p>
                            </li>
                            <li>
                                <p><code>justified</code>
                                    <em>(Defaults: false)</em> : Whether tabs fill the container and have a consistent width. </p>
                            </li>
                            <li>
                                <p><code>type</code>
                                    <em>(Defaults: 'tabs')</em> : Navigation type. Possible values are 'tabs' and 'pills'. </p>
                            </li>
                        </ul>
                        <h4><code>&lt;tab&gt;</code></h4>
                        <ul>
                            <li>
                                <p><code>heading</code> or <code>&lt;tab-heading&gt;</code> : Heading text or HTML markup.</p>
                            </li>
                            <li>
                                <p><code>active</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: false)</em> : Whether tab is currently selected. </p>
                            </li>
                            <li>
                                <p><code>disabled</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: false)</em> : Whether tab is clickable and can be activated. </p>
                            </li>
                            <li>
                                <p><code>select()</code>
                                    <em>(Defaults: null)</em> : An optional expression called when tab is activated. </p>
                            </li>
                            <li>
                                <p><code>deselect()</code>
                                    <em>(Defaults: null)</em> : An optional expression called when tab is deactivated. </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TABS DEMO -->
        <!-- BEGIN CAROUSEL DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Carousel</span>
                    <span class="caption-helper">ui.bootstrap.carousel</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="CarouselDemoCtrl">
                            <div style="height: 305px">
                                <uib-carousel active="active" interval="myInterval" no-wrap="noWrapSlides">
                                    <uib-slide ng-repeat="slide in slides track by slide.id" index="slide.id">
                                        <img ng-src="{{slide.image}}" style="margin:auto;">
                                        <div class="carousel-caption">
                                            <h4>Slide {{slide.id}}</h4>
                                            <p>{{slide.text}}</p>
                                        </div>
                                    </uib-slide>
                                </uib-carousel>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info" ng-click="addSlide()">Add Slide</button>
                                    <button type="button" class="btn btn-info" ng-click="randomize()">Randomize slides</button>
                                    <label class="mt-checkbox"> Disable Slide Looping
                                        <input type="checkbox" ng-model="noWrapSlides">
                                        <span></span>
                                    </label>
                                </div>
                                <div class="col-md-6"> Interval, in milliseconds:
                                    <input type="number" class="form-control" ng-model="myInterval">
                                    <br />Enter a negative number or 0 to stop the interval. </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Carousel creates a carousel similar to bootstrap's image carousel.</p>
                        <p>The carousel also offers support for touchscreen devices in the form of swiping. To enable swiping, load the <code>ngTouch</code> module as a dependency.</p>
                        <p>Use a <code>&lt;carousel&gt;</code> element with <code>&lt;slide&gt;</code> elements inside it. It will automatically cycle through the slides at a given rate, and a current-index variable will be kept in sync with the currently
                            visible slide. </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CAROUSEL DEMO -->
        <!-- BEGIN COLLAPSE DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Collapse</span>
                    <span class="caption-helper">ui.bootstrap.collapse</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="CollapseDemoCtrl">
                            <button type="button" class="btn btn-default" ng-click="isCollapsed = !isCollapsed">Toggle collapse</button>
                            <hr>
                            <div uib-collapse="isCollapsed">
                                <div class="well well-lg">Some content</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>AngularJS version of Bootstrap's collapse plugin. Provides a simple way to hide and show an element with a css transition.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END COLLAPSE DEMO -->
        <!-- BEGIN DATEPICKER DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Datepicker</span>
                    <span class="caption-helper">ui.bootstrap.datepicker</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="DatepickerDemoCtrl"> <pre>Selected date is: <em>{{dt | date:'fullDate' }}</em></pre>
                            <h4>Inline</h4>
                            <div style="display:inline-block; min-height:290px;">
                                <uib-datepicker ng-model="dt" class="well well-sm" datepicker-options="options"></uib-datepicker>
                            </div>
                            <hr />
                            <button type="button" class="btn btn-sm btn-info" ng-click="today()">Today</button>
                            <button type="button" class="btn btn-sm btn-default" ng-click="setDate(2009, 7, 24)">2009-08-24</button>
                            <button type="button" class="btn btn-sm btn-danger" ng-click="clear()">Clear</button>
                            <button type="button" class="btn btn-sm btn-default" ng-click="toggleMin()" uib-tooltip="After today restriction">Min date</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="scroller" style="height: 600px">
                            <p>A clean, flexible, and fully customizable date picker.</p>
                            <p>User can navigate through months and years. The datepicker shows dates that come from other than the main month being displayed. These other dates are also selectable.</p>
                            <p>Everything is formatted using the
                                <a href="http://docs.angularjs.org/api/ng.filter:date">date filter</a> and thus is also localized. </p>
                            <h3>Datepicker Settings</h3>
                            <p>All settings can be provided as attributes in the <code>datepicker</code> or globally configured through the <code>datepickerConfig</code>.</p>
                            <ul>
                                <li>
                                    <p><code>ng-model</code>
                                        <i class="glyphicon glyphicon-eye-open"></i> : The date object. </p>
                                </li>
                                <li>
                                    <p><code>datepicker-mode</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: 'day')</em> : Current mode of the datepicker
                                        <em>(day|month|year)</em>. Can be used to initialize datepicker to specific mode. </p>
                                </li>
                                <li>
                                    <p><code>min-date</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Default: null)</em> : Defines the minimum available date. </p>
                                </li>
                                <li>
                                    <p><code>max-date</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Default: null)</em> : Defines the maximum available date. </p>
                                </li>
                                <li>
                                    <p><code>date-disabled (date, mode)</code>
                                        <em>(Default: null)</em> : An optional expression to disable visible options based on passing date and current mode
                                        <em>(day|month|year)</em>. </p>
                                </li>
                                <li>
                                    <p><code>show-weeks</code>
                                        <em>(Defaults: true)</em> : Whether to display week numbers. </p>
                                </li>
                                <li>
                                    <p><code>starting-day</code>
                                        <em>(Defaults: 0)</em> : Starting day of the week from 0-6 (0=Sunday, ..., 6=Saturday). </p>
                                </li>
                                <li>
                                    <p><code>init-date</code> : The initial date view when no model value is not specified.</p>
                                </li>
                                <li>
                                    <p><code>min-mode</code>
                                        <em>(Defaults: 'day')</em> : Set a lower limit for mode. </p>
                                </li>
                                <li>
                                    <p><code>max-mode</code>
                                        <em>(Defaults: 'year')</em> : Set an upper limit for mode. </p>
                                </li>
                                <li>
                                    <p><code>format-day</code>
                                        <em>(Default: 'dd')</em> : Format of day in month. </p>
                                </li>
                                <li>
                                    <p><code>format-month</code>
                                        <em>(Default: 'MMMM')</em> : Format of month in year. </p>
                                </li>
                                <li>
                                    <p><code>format-year</code>
                                        <em>(Default: 'yyyy')</em> : Format of year in year range. </p>
                                </li>
                                <li>
                                    <p><code>format-day-header</code>
                                        <em>(Default: 'EEE')</em> : Format of day in week header. </p>
                                </li>
                                <li>
                                    <p><code>format-day-title</code>
                                        <em>(Default: 'MMMM yyyy')</em> : Format of title when selecting day. </p>
                                </li>
                                <li>
                                    <p><code>format-month-title</code>
                                        <em>(Default: 'yyyy')</em> : Format of title when selecting month. </p>
                                </li>
                                <li>
                                    <p><code>year-range</code>
                                        <em>(Default: 20)</em> : Number of years displayed in year selection. </p>
                                </li>
                            </ul>
                            <h3>Popup Settings</h3>
                            <p>Options for datepicker can be passed as JSON using the <code>datepicker-options</code> attribute. Specific settings for the <code>datepicker-popup</code>, that can globally configured through the <code>datepickerPopupConfig</code>,
                                are: </p>
                            <ul>
                                <li>
                                    <p><code>datepicker-popup</code>
                                        <em>(Default: 'yyyy-MM-dd')</em> : The format for displayed dates. </p>
                                </li>
                                <li>
                                    <p><code>show-button-bar</code>
                                        <em>(Default: true)</em> : Whether to display a button bar underneath the datepicker. </p>
                                </li>
                                <li>
                                    <p><code>current-text</code>
                                        <em>(Default: 'Today')</em> : The text to display for the current day button. </p>
                                </li>
                                <li>
                                    <p><code>clear-text</code>
                                        <em>(Default: 'Clear')</em> : The text to display for the clear button. </p>
                                </li>
                                <li>
                                    <p><code>close-text</code>
                                        <em>(Default: 'Done')</em> : The text to display for the close button. </p>
                                </li>
                                <li>
                                    <p><code>close-on-date-selection</code>
                                        <em>(Default: true)</em> : Whether to close calendar when a date is chosen. </p>
                                </li>
                                <li>
                                    <p><code>datepicker-append-to-body</code>
                                        <em>(Default: false)</em>: Append the datepicker popup element to <code>body</code>, rather than inserting after <code>datepicker-popup</code>. For global configuration, use <code>datepickerPopupConfig.appendToBody</code>.
                                        </p>
                                </li>
                            </ul>
                            <h3>Keyboard Support</h3>
                            <p>Depending on datepicker's current mode, the date may reffer either to day, month or year. Accordingly, the term view reffers either to a month, year or year range.</p>
                            <ul>
                                <li><code>Left</code>: Move focus to the previous date. Will move to the last date of the previous view, if the current date is the first date of a view.</li>
                                <li><code>Right</code>: Move focus to the next date. Will move to the first date of the following view, if the current date is the last date of a view.</li>
                                <li><code>Up</code>: Move focus to the same column of the previous row. Will wrap to the appropriate row in the previous view.</li>
                                <li><code>Down</code>: Move focus to the same column of the following row. Will wrap to the appropriate row in the following view.</li>
                                <li><code>PgUp</code>: Move focus to the same date of the previous view. If that date does not exist, focus is placed on the last date of the month.</li>
                                <li><code>PgDn</code>: Move focus to the same date of the following view. If that date does not exist, focus is placed on the last date of the month.</li>
                                <li><code>Home</code>: Move to the first date of the view.</li>
                                <li><code>End</code>: Move to the last date of the view.</li>
                                <li><code>Enter</code>/<code>Space</code>: Select date.</li>
                                <li><code>Ctrl</code>+<code>Up</code>: Move to an upper mode.</li>
                                <li><code>Ctrl</code>+<code>Down</code>: Move to a lower mode.</li>
                                <li><code>Esc</code>: Will close popup, and move focus to the input.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DATEPICKER DEMO -->
        <!-- BEGIN DATEPICKER DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Datepicker Popup</span>
                    <span class="caption-helper">ui.bootstrap.datepickerPopup</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <style>
                            .full button span {
                                background-color: limegreen;
                                border-radius: 32px;
                                color: black;
                            }
                            
                            .partially button span {
                                background-color: orange;
                                border-radius: 32px;
                                color: black;
                            }
                        </style>
                        <div ng-controller="DatepickerPopupDemoCtrl"> <pre>Selected date is: <em>{{dt | date:'fullDate' }}</em></pre>
                            <h4>Popup</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="input-group">
                                        <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="dt" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="true" close-text="Close" alt-input-formats="altInputFormats"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="open1()">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </button>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="input-group">
                                        <input type="text" class="form-control" uib-datepicker-popup ng-model="dt" is-open="popup2.opened" datepicker-options="dateOptions" ng-required="true" close-text="Close" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="open2()">
                                                <i class="glyphicon glyphicon-calendar"></i>
                                            </button>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Format:
                                        <span class="muted-text">(manual alternate
                                            <em>{{altInputFormats[0]}}</em>)</span>
                                    </label>
                                    <select class="form-control" ng-model="format" ng-options="f for f in formats">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <hr />
                            <button type="button" class="btn btn-sm btn-info" ng-click="today()">Today</button>
                            <button type="button" class="btn btn-sm btn-default" ng-click="setDate(2009, 7, 24)">2009-08-24</button>
                            <button type="button" class="btn btn-sm btn-danger" ng-click="clear()">Clear</button>
                            <button type="button" class="btn btn-sm btn-default" ng-click="toggleMin()" uib-tooltip="After today restriction">Min date</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>The datepicker popup is meant to be used with an input element. To understand usage of the datepicker, please refer to its documentation
                            <a href="https://angular-ui.github.io/bootstrap/#/datepicker">here</a>.</p>
                        <h3 id="uib-datepicker-popup-settings">uib-datepicker-popup settings</h3>
                        <p>The popup is a wrapper that you can use in an input to toggle a datepicker. To configure the datepicker, use <code>datepicker-options</code> as documented in the
                            <a href="https://angular-ui.github.io/bootstrap/#/datepicker">inline datepicker</a>.</p>
                        <ul>
                            <li>
                                <p><code>alt-input-formats</code>
                                    <small class="badge">$</small>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>[]</code>)</em> - A list of alternate formats acceptable for manual entry. </p>
                            </li>
                            <li>
                                <p><code>clear-text</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>Clear</code>)</em> - The text to display for the clear button. </p>
                            </li>
                            <li>
                                <p><code>close-on-date-selection</code>
                                    <small class="badge">$</small>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>true</code>)</em> - Whether to close calendar when a date is chosen. </p>
                            </li>
                            <li>
                                <p><code>close-text</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>Done</code>)</em> - The text to display for the close button. </p>
                            </li>
                            <li>
                                <p><code>current-text</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>Today</code>)</em> - The text to display for the current day button. </p>
                            </li>
                            <li>
                                <p><code>datepicker-append-to-body</code>
                                    <small class="badge">$</small>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>false</code>, Config: <code>appendToBody</code>)</em> - Append the datepicker popup element to <code>body</code>, rather than inserting after <code>datepicker-popup</code>. </p>
                            </li>
                            <li>
                                <p><code>datepicker-options</code>
                                    <small class="badge">$</small> - An object with any combination of the datepicker settings (in camelCase) used to configure the wrapped datepicker. </p>
                            </li>
                            <li>
                                <p><code>datepicker-popup-template-url</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>uib/template/datepickerPopup/popup.html</code>)</em> - Add the ability to override the template used on the component. </p>
                            </li>
                            <li>
                                <p><code>datepicker-template-url</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>uib/template/datepicker/datepicker.html</code>)</em> - Add the ability to override the template used on the component (inner uib-datepicker). </p>
                            </li>
                            <li>
                                <p><code>is-open</code>
                                    <small class="badge">$</small>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Default: <code>false</code>)</em> - Whether or not to show the datepicker. </p>
                            </li>
                            <li>
                                <p><code>ng-model</code>
                                    <small class="badge">$</small>
                                    <i class="glyphicon glyphicon-eye-open"></i> - The date object. Must be a Javascript <code>Date</code> object. You may use the <code>uibDateParser</code> service to assist in string-to-object conversion. </p>
                            </li>
                            <li>
                                <p><code>on-open-focus</code>
                                    <small class="badge">$</small>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>true</code>)</em> - Whether or not to focus the datepicker popup upon opening. </p>
                            </li>
                            <li>
                                <p><code>show-button-bar</code>
                                    <small class="badge">$</small>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>true</code>)</em> - Whether or not to display a button bar underneath the uib-datepicker. </p>
                            </li>
                            <li>
                                <p><code>type</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>text</code>, Config: <code>html5Types</code>)</em> - You can override the input type to be
                                    <em>(date|datetime-local|month)</em>. That will change the date format of the popup. </p>
                            </li>
                            <li>
                                <p><code>popup-placement</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>auto bottom-left</code>, Config: &#39;placement&#39;)</em> - Passing in &#39;auto&#39; separated by a space before the placement will enable auto positioning, e.g: &quot;auto bottom-left&quot;. The popup
                                    will attempt to position where it fits in the closest scrollable ancestor. Accepts: </p>
                                <ul>
                                    <li><code>top</code> - popup on top, horizontally centered on input element.</li>
                                    <li><code>top-left</code> - popup on top, left edge aligned with input element left edge.</li>
                                    <li><code>top-right</code> - popup on top, right edge aligned with input element right edge.</li>
                                    <li><code>bottom</code> - popup on bottom, horizontally centered on input element.</li>
                                    <li><code>bottom-left</code> - popup on bottom, left edge aligned with input element left edge.</li>
                                    <li><code>bottom-right</code> - popup on bottom, right edge aligned with input element right edge.</li>
                                    <li><code>left</code> - popup on left, vertically centered on input element.</li>
                                    <li><code>left-top</code> - popup on left, top edge aligned with input element top edge.</li>
                                    <li><code>left-bottom</code> - popup on left, bottom edge aligned with input element bottom edge.</li>
                                    <li><code>right</code> - popup on right, vertically centered on input element.</li>
                                    <li><code>right-top</code> - popup on right, top edge aligned with input element top edge.</li>
                                    <li><code>right-bottom</code> - popup on right, bottom edge aligned with input element bottom edge.</li>
                                </ul>
                            </li>
                            <li>
                                <p><code>uib-datepicker-popup</code>
                                    <small class="badge">C</small>
                                    <em>(Default: <code>yyyy-MM-dd</code>, Config: <code>datepickerConfig</code>)</em> - The format for displayed dates. This string can take string literals by surrounding the value with single quotes, i.e. <code>yyyy-MM-dd h &#39;o\&#39;clock&#39;</code>.
                                    </p>
                            </li>
                        </ul>
                        <p>
                            <strong>Notes</strong>
                        </p>
                        <p>If using this directive on input type date, a native browser datepicker could also appear.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DATEPICKER DEMO -->
        <!-- BEGIN DATEPICKER DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Timepicker</span>
                    <span class="caption-helper">ui.bootstrap.timepicker</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="TimepickerDemoCtrl">
                            <uib-timepicker ng-model="mytime" ng-change="changed()" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></uib-timepicker> <pre class="alert alert-info">Time is: {{mytime | date:'shortTime' }}</pre>
                            <div class="row">
                                <div class="col-xs-6"> Hours step is:
                                    <select class="form-control" ng-model="hstep" ng-options="opt for opt in options.hstep"></select>
                                </div>
                                <div class="col-xs-6"> Minutes step is:
                                    <select class="form-control" ng-model="mstep" ng-options="opt for opt in options.mstep"></select>
                                </div>
                            </div>
                            <hr>
                            <button type="button" class="btn btn-info" ng-click="toggleMode()">12H / 24H</button>
                            <button type="button" class="btn btn-default" ng-click="update()">Set to 14:00</button>
                            <button type="button" class="btn btn-danger" ng-click="clear()">Clear</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>A lightweight &amp; configurable timepicker directive.</p>
                        <h3>Settings</h3>
                        <p>All settings can be provided as attributes in the <code>&lt;timepicker&gt;</code> or globally configured through the <code>timepickerConfig</code>.</p>
                        <ul>
                            <li>
                                <p><code>ng-model</code>
                                    <i class="glyphicon glyphicon-eye-open"></i> : The Date object that provides the time state. </p>
                            </li>
                            <li>
                                <p><code>hour-step</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: 1)</em> : Number of hours to increase or decrease when using a button. </p>
                            </li>
                            <li>
                                <p><code>minute-step</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: 1)</em> : Number of minutes to increase or decrease when using a button. </p>
                            </li>
                            <li>
                                <p><code>show-meridian</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: true)</em> : Whether to display 12H or 24H mode. </p>
                            </li>
                            <li>
                                <p><code>meridians</code>
                                    <em>(Defaults: null)</em> : Meridian labels based on locale. To override you must supply an array like ['AM', 'PM']. </p>
                            </li>
                            <li>
                                <p><code>readonly-input</code>
                                    <em>(Defaults: false)</em> : Whether user can type inside the hours &amp; minutes input. </p>
                            </li>
                            <li>
                                <p><code>mousewheel</code>
                                    <em>(Defaults: true)</em> : Whether user can scroll inside the hours &amp; minutes input to increase or decrease it's values. </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DATEPICKER DEMO -->
        <!-- BEGIN DROPDOWN DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Dropdown</span>
                    <span class="caption-helper">ui.bootstrap.dropdown</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="DropdownCtrl">
                            <!-- Simple dropdown -->
                            <span uib-dropdown on-toggle="toggled(open)">
                                <p>
                                    <a href id="simple-dropdown" uib-dropdown-toggle> Click me for a dropdown, yo! </a>
                                </p>
                                <ul class="dropdown-menu" uib-dropdown-menu aria-labelledby="simple-dropdown">
                                    <li ng-repeat="choice in items">
                                        <a href>{{choice}}</a>
                                    </li>
                                </ul>
                            </span>
                            <!-- Single button -->
                            <div class="btn-group" uib-dropdown is-open="status.isopen">
                                <button id="single-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled"> Button dropdown
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="single-button">
                                    <li role="menuitem">
                                        <a href="#">Action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Another action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li role="menuitem">
                                        <a href="#">Separated link</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Split button -->
                            <div class="btn-group" uib-dropdown>
                                <button id="split-button" type="button" class="btn btn-danger">Action</button>
                                <button type="button" class="btn btn-danger" uib-dropdown-toggle>
                                    <span class="caret"></span>
                                    <span class="sr-only">Split button!</span>
                                </button>
                                <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="split-button">
                                    <li role="menuitem">
                                        <a href="#">Action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Another action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li role="menuitem">
                                        <a href="#">Separated link</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Single button using append-to-body -->
                            <div class="btn-group" uib-dropdown dropdown-append-to-body>
                                <button id="btn-append-to-body" type="button" class="btn btn-primary" uib-dropdown-toggle> Dropdown on Body
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-body">
                                    <li role="menuitem">
                                        <a href="#">Action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Another action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li role="menuitem">
                                        <a href="#">Separated link</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Single button using template-url -->
                            <div class="btn-group" uib-dropdown>
                                <button id="button-template-url" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled"> Dropdown using template
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" uib-dropdown-menu template-url="dropdown.html" aria-labelledby="button-template-url"> </ul>
                            </div>
                            <hr />
                            <p>
                                <button type="button" class="btn btn-default btn-sm" ng-click="toggleDropdown($event)">Toggle button dropdown</button>
                                <button type="button" class="btn btn-warning btn-sm" ng-click="disabled = !disabled">Enable/Disable</button>
                            </p>
                            <hr>
                            <!-- Single button with keyboard nav -->
                            <div class="btn-group" uib-dropdown keyboard-nav>
                                <button id="simple-btn-keyboard-nav" type="button" class="btn btn-primary" uib-dropdown-toggle> Dropdown with keyboard navigation
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="simple-btn-keyboard-nav">
                                    <li role="menuitem">
                                        <a href="#">Action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Another action</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li role="menuitem">
                                        <a href="#">Separated link</a>
                                    </li>
                                </ul>
                            </div>
                            <hr>
                            <!-- AppendTo use case -->
                            <h4>append-to vs. append-to-body vs. inline example</h4>
                            <div id="dropdown-scrollable-container" style="height: 15em; overflow: auto;">
                                <div id="dropdown-long-content">
                                    <div id="dropdown-hidden-container">
                                        <div class="btn-group" uib-dropdown keyboard-nav dropdown-append-to="appendToEl">
                                            <button id="btn-append-to" type="button" class="btn btn-primary" uib-dropdown-toggle> Dropdown in Container
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to">
                                                <li role="menuitem">
                                                    <a href="#">Action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Another action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Something else here</a>
                                                </li>
                                                <li class="divider"></li>
                                                <li role="menuitem">
                                                    <a href="#">Separated link</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="btn-group" uib-dropdown dropdown-append-to-body>
                                            <button id="btn-append-to-to-body" type="button" class="btn btn-primary" uib-dropdown-toggle> Dropdown on Body
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-to-body">
                                                <li role="menuitem">
                                                    <a href="#">Action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Another action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Something else here</a>
                                                </li>
                                                <li class="divider"></li>
                                                <li role="menuitem">
                                                    <a href="#">Separated link</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="btn-group" uib-dropdown>
                                            <button id="btn-append-to-single-button" type="button" class="btn btn-primary" uib-dropdown-toggle> Inline Dropdown
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-single-button">
                                                <li role="menuitem">
                                                    <a href="#">Action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Another action</a>
                                                </li>
                                                <li role="menuitem">
                                                    <a href="#">Something else here</a>
                                                </li>
                                                <li class="divider"></li>
                                                <li role="menuitem">
                                                    <a href="#">Separated link</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script type="text/ng-template" id="dropdown.html">
                                <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="button-template-url">
                                    <li role="menuitem">
                                        <a href="#">Action in Template</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Another action in Template</a>
                                    </li>
                                    <li role="menuitem">
                                        <a href="#">Something else here</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li role="menuitem">
                                        <a href="#">Separated link in Template</a>
                                    </li>
                                </ul>
                            </script>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Dropdown is a simple directive which will toggle a dropdown menu on click or programmatically. You can either use <code>is-open</code> to toggle or add inside a <code>&lt;a dropdown-toggle&gt;</code> element to toggle it when is
                            clicked. There is also the <code>on-toggle(open)</code> optional expression fired when dropdown changes state. </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DROPDOWN DEMO -->
        <!-- BEGIN MODAL DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Modal</span>
                    <span class="caption-helper">ui.bootstrap.modal</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="ModalDemoCtrl">
                            <script type="text/ng-template" id="myModalContent.html">
                                <div class="modal-header">
                                    <h3 class="modal-title">I'm a modal!</h3>
                                </div>
                                <div class="modal-body">
                                    <ul>
                                        <li ng-repeat="item in items">
                                            <a href="#" ng-click="$event.preventDefault(); selected.item = item">{{ item }}</a>
                                        </li>
                                    </ul> Selected:
                                    <b>{{ selected.item }}</b>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" ng-click="ok()">OK</button>
                                    <button class="btn btn-warning" type="button" ng-click="cancel()">Cancel</button>
                                </div>
                            </script>
                            <button type="button" class="btn btn-default" ng-click="open()">Open me!</button>
                            <button type="button" class="btn btn-default" ng-click="open('lg')">Large modal</button>
                            <button type="button" class="btn btn-default" ng-click="open('sm')">Small modal</button>
                            <button type="button" class="btn btn-default" ng-click="toggleAnimation()">Toggle Animation ({{ animationsEnabled }})</button>
                            <div ng-show="selected">Selection from a modal: {{ selected }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="scroller" style="height: 400px">
                            <p><code>$modal</code> is a service to quickly create AngularJS-powered modal windows. Creating custom modals is straightforward: create a partial view, its controller and reference them when using the service.</p>
                            <p>The <code>$modal</code> service has only one method: <code>open(options)</code> where available options are like follows:</p>
                            <ul>
                                <li><code>templateUrl</code> - a path to a template representing modal's content</li>
                                <li><code>template</code> - inline template representing the modal's content</li>
                                <li><code>scope</code> - a scope instance to be used for the modal's content (actually the <code>$modal</code> service is going to create a child scope of a provided scope). Defaults to <code>$rootScope</code></li>
                                <li><code>controller</code> - a controller for a modal instance - it can initialize scope used by modal. Accepts the "controller-as" syntax in the form 'SomeCtrl as myctrl'; can be injected with <code>$modalInstance</code></li>
                                <li><code>controllerAs</code> - an alternative to the controller-as syntax, matching the API of directive definitions. Requires the <code>controller</code> option to be provided as well</li>
                                <li><code>resolve</code> - members that will be resolved and passed to the controller as locals; it is equivalent of the <code>resolve</code> property for AngularJS routes</li>
                                <li><code>backdrop</code> - controls presence of a backdrop. Allowed values: true (default), false (no backdrop), <code>'static'</code> - backdrop is present but modal window is not closed when clicking outside of the modal
                                    window. </li>
                                <li><code>keyboard</code> - indicates whether the dialog should be closable by hitting the ESC key, defaults to true</li>
                                <li><code>backdropClass</code> - additional CSS class(es) to be added to a modal backdrop template</li>
                                <li><code>windowClass</code> - additional CSS class(es) to be added to a modal window template</li>
                                <li><code>windowTemplateUrl</code> - a path to a template overriding modal's window template</li>
                                <li><code>size</code> - optional size of modal window. Allowed values: <code>'sm'</code> (small) or <code>'lg'</code> (large). Requires Bootstrap 3.1.0 or later</li>
                            </ul>
                            <p>The <code>open</code> method returns a modal instance, an object with the following properties:</p>
                            <ul>
                                <li><code>close(result)</code> - a method that can be used to close a modal, passing a result</li>
                                <li><code>dismiss(reason)</code> - a method that can be used to dismiss a modal, passing a reason</li>
                                <li><code>result</code> - a promise that is resolved when a modal is closed and rejected when a modal is dismissed</li>
                                <li><code>opened</code> - a promise that is resolved when a modal gets opened after downloading content's template and resolving all variables</li>
                            </ul>
                            <p>In addition the scope associated with modal's content is augmented with 2 methods:</p>
                            <ul>
                                <li><code>$close(result)</code></li>
                                <li><code>$dismiss(reason)</code></li>
                            </ul>
                            <p>Those methods make it easy to close a modal window without a need to create a dedicated controller.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END DROPDOWN DEMO -->
        <!-- BEGIN PAGINATION DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Pagination</span>
                    <span class="caption-helper">ui.bootstrap.pagination</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="PaginationDemoCtrl">
                            <h4>Default</h4>
                            <uib-pagination total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()"></uib-pagination>
                            <uib-pagination boundary-links="true" total-items="totalItems" ng-model="currentPage" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></uib-pagination>
                            <uib-pagination direction-links="false" boundary-links="true" total-items="totalItems" ng-model="currentPage"></uib-pagination>
                            <uib-pagination direction-links="false" total-items="totalItems" ng-model="currentPage" num-pages="smallnumPages"></uib-pagination> <pre>The selected page no: {{currentPage}}</pre>
                            <button type="button" class="btn btn-info" ng-click="setPage(3)">Set current page to: 3</button>
                            <hr />
                            <h4>Limit the maximum visible buttons</h4>
                            <h6><code>rotate</code> defaulted to <code>true</code>:</h6>
                            <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-links="true" num-pages="numPages"></uib-pagination>
                            <h6><code>rotate</code> defaulted to <code>true</code> and <code>force-ellipses</code> set to <code>true</code>:</h6>
                            <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm"
                                boundary-links="true" force-ellipses="true"></uib-pagination>
                            <h6><code>rotate</code> set to <code>false</code>:</h6>
                            <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-links="true" rotate="false"></uib-pagination>
                            <h6><code>boundary-link-numbers</code> set to <code>true</code> and <code>rotate</code> defaulted to <code>true</code>:</h6>
                            <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm"
                                boundary-link-numbers="true"></uib-pagination>
                            <h6><code>boundary-link-numbers</code> set to <code>true</code> and <code>rotate</code> set to <code>false</code>:</h6>
                            <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm"
                                boundary-link-numbers="true" rotate="false"></uib-pagination> <pre>Page: {{bigCurrentPage}} / {{numPages}}</pre> </div>
                    </div>
                    <div class="col-md-6">
                        <p>A lightweight pagination directive that is focused on ... providing pagination &amp; will take care of visualising a pagination bar and enable / disable buttons correctly!</p>
                        <h3>Pagination Settings</h3>
                        <p>Settings can be provided as attributes in the <code>&lt;pagination&gt;</code> or globally configured through the <code>paginationConfig</code>.</p>
                        <ul>
                            <li>
                                <p><code>ng-model</code>
                                    <i class="glyphicon glyphicon-eye-open"></i> : Current page number. First page is 1. </p>
                            </li>
                            <li>
                                <p><code>total-items</code>
                                    <i class="glyphicon glyphicon-eye-open"></i> : Total number of items in all pages. </p>
                            </li>
                            <li>
                                <p><code>items-per-page</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: 10)</em> : Maximum number of items per page. A value less than one indicates all items on one page. </p>
                            </li>
                            <li>
                                <p><code>max-size</code>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <em>(Defaults: null)</em> : Limit number for pagination size. </p>
                            </li>
                            <li>
                                <p><code>num-pages</code>
                                    <small class="badge">readonly</small>
                                    <em>(Defaults: angular.noop)</em> : An optional expression assigned the total number of pages to display. </p>
                            </li>
                            <li>
                                <p><code>rotate</code>
                                    <em>(Defaults: true)</em> : Whether to keep current page in the middle of the visible ones. </p>
                            </li>
                            <li>
                                <p><code>direction-links</code>
                                    <em>(Default: true)</em> : Whether to display Previous / Next buttons. </p>
                            </li>
                            <li>
                                <p><code>previous-text</code>
                                    <em>(Default: 'Previous')</em> : Text for Previous button. </p>
                            </li>
                            <li>
                                <p><code>next-text</code>
                                    <em>(Default: 'Next')</em> : Text for Next button. </p>
                            </li>
                            <li>
                                <p><code>boundary-links</code>
                                    <em>(Default: false)</em> : Whether to display First / Last buttons. </p>
                            </li>
                            <li>
                                <p><code>first-text</code>
                                    <em>(Default: 'First')</em> : Text for First button. </p>
                            </li>
                            <li>
                                <p><code>last-text</code>
                                    <em>(Default: 'Last')</em> : Text for Last button. </p>
                            </li>
                        </ul>
                        <h3>Pager Settings</h3>
                        <p>Settings can be provided as attributes in the <code>&lt;pager&gt;</code> or globally configured through the <code>pagerConfig</code>.
                            <br> For <code>ng-model</code>, <code>total-items</code>, <code>items-per-page</code> and <code>num-pages</code> see pagination settings. Other settings are: </p>
                        <ul>
                            <li>
                                <p><code>align</code>
                                    <em>(Default: true)</em> : Whether to align each link to the sides. </p>
                            </li>
                            <li>
                                <p><code>previous-text</code>
                                    <em>(Default: '« Previous')</em> : Text for Previous button. </p>
                            </li>
                            <li>
                                <p><code>next-text</code>
                                    <em>(Default: 'Next »')</em> : Text for Next button. </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGINATION DEMO -->
        <!-- BEGIN POPOVER DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Popover</span>
                    <span class="caption-helper">ui.bootstrap.popover</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="PopoverDemoCtrl">
                            <h4>Dynamic</h4>
                            <div class="form-group">
                                <label>Popup Text:</label>
                                <input type="text" ng-model="dynamicPopover.content" class="form-control"> </div>
                            <div class="form-group">
                                <label>Popup Title:</label>
                                <input type="text" ng-model="dynamicPopover.title" class="form-control"> </div>
                            <div class="form-group">
                                <label>Popup Template:</label>
                                <input type="text" ng-model="dynamicPopover.templateUrl" class="form-control"> </div>
                            <button uib-popover="{{dynamicPopover.content}}" popover-title="{{dynamicPopover.title}}" type="button" class="btn btn-default">Dynamic Popover</button>
                            <button uib-popover-template="dynamicPopover.templateUrl" popover-title="{{dynamicPopover.title}}" type="button" class="btn btn-default">Popover With Template</button>
                            <script type="text/ng-template" id="myPopoverTemplate.html">
                                <div>{{dynamicPopover.content}}</div>
                                <div class="form-group">
                                    <label>Popup Title:</label>
                                    <input type="text" ng-model="dynamicPopover.title" class="form-control"> </div>
                            </script>
                            <hr />
                            <h4>Positional</h4>
                            <div class="form-group">
                                <label>Popover placement</label>
                                <select class="form-control" ng-model="placement.selected" ng-options="o as o for o in placement.options"></select>
                            </div>
                            <button popover-placement="{{placement.selected}}" uib-popover="On the {{placement.selected}}" type="button" class="btn btn-default">Popover {{placement.selected}}</button>
                            <hr />
                            <h4>Triggers</h4>
                            <p>
                                <button uib-popover="I appeared on mouse enter!" popover-trigger="mouseenter" type="button" class="btn btn-default">Mouseenter</button>
                            </p>
                            <input type="text" value="Click me!" uib-popover="I appeared on focus! Click away and I'll vanish..." popover-trigger="focus" class="form-control">
                            <hr />
                            <h4>Other</h4>
                            <button popover-animation="true" uib-popover="I fade in and out!" type="button" class="btn btn-default">fading</button>
                            <button uib-popover="I have a title!" popover-title="The title." type="button" class="btn btn-default">title</button>
                            <button uib-popover="I am activated manually" popover-is-open="popoverIsOpen" ng-click="popoverIsOpen = !popoverIsOpen" type="button" class="btn btn-default">Toggle popover</button>
                            <button uib-popover-html="htmlPopover" class="btn btn-default">HTML Popover</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>A lightweight, extensible directive for fancy popover creation. The popover directive supports multiple placements, optional transition animation, and more.</p>
                        <p>Like the Bootstrap jQuery plugin, the popover
                            <strong>requires</strong> the tooltip module. </p>
                        <p>The popover directives provides several optional attributes to control how it will display:</p>
                        <ul>
                            <li><code>popover-title</code>: A string to display as a fancy title.</li>
                            <li><code>popover-placement</code>: Where to place it? Defaults to "top", but also accepts "bottom", "left", "right".</li>
                            <li><code>popover-animation</code>: Should it fade in and out? Defaults to "true".</li>
                            <li><code>popover-popup-delay</code>: For how long should the user have to have the mouse over the element before the popover shows (in milliseconds)? Defaults to 0.</li>
                            <li><code>popover-trigger</code>: What should trigger the show of the popover? See the <code>tooltip</code> directive for supported values.</li>
                            <li><code>popover-append-to-body</code>: Should the tooltip be appended to <code>$body</code> instead of the parent element?</li>
                        </ul>
                        <p>The popover directives require the <code>$position</code> service.</p>
                        <p>The popover directive also supports various default configurations through the $tooltipProvider. See the
                            <a href="#tooltip">tooltip</a> section for more information. </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END POPOVER DEMO -->
        <!-- BEGIN TOOLTIP DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Tooltip</span>
                    <span class="caption-helper">ui.bootstrap.tooltip</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="TooltipDemoCtrl">
                            <div class="form-group">
                                <label>Dynamic Tooltip Text</label>
                                <input type="text" ng-model="dynamicTooltipText" class="form-control"> </div>
                            <div class="form-group">
                                <label>Dynamic Tooltip Popup Text</label>
                                <input type="text" ng-model="dynamicTooltip" class="form-control"> </div>
                            <p> Pellentesque
                                <a href="#" tooltip="{{dynamicTooltip}}">{{dynamicTooltipText}}</a>, sit amet venenatis urna cursus eget nunc scelerisque viverra mauris, in aliquam. Tincidunt lobortis feugiat vivamus at
                                <a href="#" tooltip-placement="left" tooltip="On the Left!">left</a> eget arcu dictum varius duis at consectetur lorem. Vitae elementum curabitur
                                <a href="#" tooltip-placement="right" tooltip="On the Right!">right</a> nunc sed velit dignissim sodales ut eu sem integer vitae. Turpis egestas
                                <a href="#" tooltip-placement="bottom" tooltip="On the Bottom!">bottom</a> pharetra convallis posuere morbi leo urna,
                                <a href="#" tooltip-animation="false" tooltip="I don't fade. :-(">fading</a> at elementum eu, facilisis sed odio morbi quis commodo odio. In cursus
                                <a href="#" tooltip-popup-delay='1000' tooltip='appears with delay'>delayed</a> turpis massa tincidunt dui ut. </p>
                            <p> I can even contain HTML.
                                <a href="#" tooltip-html-unsafe="{{htmlTooltip}}">Check me out!</a>
                            </p>
                            <form role="form">
                                <div class="form-group">
                                    <label>Or use custom triggers, like focus: </label>
                                    <input type="text" value="Click me!" tooltip="See? Now click away..." tooltip-trigger="focus" tooltip-placement="right" class="form-control" /> </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="scroller" style="height: 400px">
                            <p>A lightweight, extensible directive for fancy tooltip creation. The tooltip directive supports multiple placements, optional transition animation, and more.</p>
                            <p>There are two versions of the tooltip: <code>tooltip</code> and <code>tooltip-html-unsafe</code>. The former takes text only and will escape any HTML provided. The latter takes whatever HTML is provided and displays it in a
                                tooltip; it called "unsafe" because the HTML is not sanitized.
                                <em>The user is responsible for ensuring the content is safe to put into the DOM!</em>
                            </p>
                            <p>The tooltip directives provide several optional attributes to control how they will display:</p>
                            <ul>
                                <li><code>tooltip-placement</code>: Where to place it? Defaults to "top", but also accepts "bottom", "left", "right".</li>
                                <li><code>tooltip-animation</code>: Should it fade in and out? Defaults to "true".</li>
                                <li><code>tooltip-popup-delay</code>: For how long should the user have to have the mouse over the element before the tooltip shows (in milliseconds)? Defaults to 0.</li>
                                <li><code>tooltip-trigger</code>: What should trigger a show of the tooltip?</li>
                                <li><code>tooltip-append-to-body</code>: Should the tooltip be appended to <code>$body</code> instead of the parent element?</li>
                            </ul>
                            <p>The tooltip directives require the <code>$position</code> service.</p>
                            <p>
                                <strong>Triggers</strong>
                            </p>
                            <p>The following show triggers are supported out of the box, along with their provided hide triggers:</p>
                            <ul>
                                <li><code>mouseenter</code>: <code>mouseleave</code></li>
                                <li><code>click</code>: <code>click</code></li>
                                <li><code>focus</code>: <code>blur</code></li>
                            </ul>
                            <p>For any non-supported value, the trigger will be used to both show and hide the tooltip.</p>
                            <p>
                                <strong>$tooltipProvider</strong>
                            </p>
                            <p>Through the <code>$tooltipProvider</code>, you can change the way tooltips and popovers behave by default; the attributes above always take precedence. The following methods are available:</p>
                            <ul>
                                <li><code>setTriggers( obj )</code>: Extends the default trigger mappings mentioned above with mappings of your own. E.g. <code>{ 'openTrigger': 'closeTrigger' }</code>.</li>
                                <li>
                                    <p><code>options( obj )</code>: Provide a set of defaults for certain tooltip and popover attributes. Currently supports 'placement', 'animation', 'popupDelay', and <code>appendToBody</code>. Here are the defaults: <code>placement: 'top'</code>,
                                        <code>animation: true</code>, <code>popupDelay: 0</code> & <code>appendToBody: false</code> </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TOOLTIP DEMO -->
        <!-- BEGIN PROGRESSBAR DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Progressbar</span>
                    <span class="caption-helper">ui.bootstrap.progressbar</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="ProgressDemoCtrl">
                            <h3>Static</h3>
                            <div class="row">
                                <div class="col-sm-4">
                                    <uib-progressbar value="55"></uib-progressbar>
                                </div>
                                <div class="col-sm-4">
                                    <uib-progressbar class="progress-striped" value="22" type="warning">22%</uib-progressbar>
                                </div>
                                <div class="col-sm-4">
                                    <uib-progressbar class="progress-striped active" max="200" value="166" type="danger">
                                        <i>166 / 200</i>
                                    </uib-progressbar>
                                </div>
                            </div>
                            <hr />
                            <h3>Dynamic
                                <button type="button" class="btn btn-sm btn-primary" ng-click="random()">Randomize</button>
                            </h3>
                            <uib-progressbar max="max" value="dynamic">
                                <span style="color:white; white-space:nowrap;">{{dynamic}} / {{max}}</span>
                            </uib-progressbar>
                            <small>
                                <em>No animation</em>
                            </small>
                            <uib-progressbar animate="false" value="dynamic" type="success">
                                <b>{{dynamic}}%</b>
                            </uib-progressbar>
                            <small>
                                <em>Object (changes type based on value)</em>
                            </small>
                            <uib-progressbar class="progress-striped active" value="dynamic" type="{{type}}">{{type}}
                                <i ng-show="showWarning">!!! Watch out !!!</i>
                            </uib-progressbar>
                            <hr />
                            <h3>Stacked
                                <button type="button" class="btn btn-sm btn-primary" ng-click="randomStacked()">Randomize</button>
                            </h3>
                            <uib-progress>
                                <uib-bar ng-repeat="bar in stacked track by $index" value="bar.value" type="{{bar.type}}">
                                    <span ng-hide="bar.value < 5">{{bar.value}}%</span>
                                </uib-bar>
                            </uib-progress>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>A progress bar directive that is focused on providing feedback on the progress of a workflow or action.</p>
                        <p>It supports multiple (stacked) bars into the same <code>&lt;progress&gt;</code> element or a single <code>&lt;progressbar&gt;</code> elemtnt with optional <code>max</code> attribute and transition animations.</p>
                        <h3>Settings</h3>
                        <h4><code>&lt;progressbar&gt;</code></h4>
                        <ul>
                            <li>
                                <p><code>value</code>
                                    <i class="glyphicon glyphicon-eye-open"></i> : The current value of progress completed. </p>
                            </li>
                            <li>
                                <p><code>type</code>
                                    <em>(Default: null)</em> : Style type. Possible values are 'success', 'warning' etc. </p>
                            </li>
                            <li>
                                <p><code>max</code>
                                    <em>(Default: 100)</em> : A number that specifies the total value of bars that is required. </p>
                            </li>
                            <li>
                                <p><code>animate</code>
                                    <em>(Default: true)</em> : Whether bars use transitions to achieve the width change. </p>
                            </li>
                        </ul>
                        <h3>Stacked</h3>
                        <p>Place multiple <code>&lt;bars&gt;</code> into the same <code>&lt;progress&gt;</code> element to stack them. <code>&lt;progress&gt;</code> supports <code>max</code> and <code>animate</code> &amp; <code>&lt;bar&gt;</code> supports
                            <code>value</code> and <code>type</code> attributes. </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROGRESSBAR DEMO -->
        <!-- BEGIN RATING DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Typeahead </span>
                    <span class="caption-helper">ui.bootstrap.typeahead</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <script type="text/ng-template" id="customTemplate.html">
                            <a>
                                <img ng-src="http://upload.wikimedia.org/wikipedia/commons/thumb/{{match.model.flag}}" width="16">
                                <span bind-html-unsafe="match.label | typeaheadHighlight:query"></span>
                            </a>
                        </script>
                        <div class='container-fluid' ng-controller="TypeaheadCtrl">
                            <h4>Static arrays</h4> <pre>Model: {{selected | json}}</pre>
                            <input type="text" ng-model="selected" typeahead="state for state in states | filter:$viewValue | limitTo:8" class="form-control">
                            <h4>Asynchronous results</h4> <pre>Model: {{asyncSelected | json}}</pre>
                            <input type="text" ng-model="asyncSelected" placeholder="Locations loaded via $http" typeahead="address for address in getLocation($viewValue)" typeahead-loading="loadingLocations"
                                class="form-control">
                            <i ng-show="loadingLocations" class="glyphicon glyphicon-refresh"></i>
                            <h4>Custom templates for results</h4> <pre>Model: {{customSelected | json}}</pre>
                            <input type="text" ng-model="customSelected" placeholder="Custom template" typeahead="state as state.name for state in statesWithFlags | filter:{name:$viewValue}"
                                typeahead-template-url="customTemplate.html" class="form-control"> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="scroller" style="height: 400px">
                            <p>Typeahead is a AngularJS version of
                                <a href="http://getbootstrap.com/2.3.2/javascript.html#typeahead">Bootstrap v2's typeahead plugin</a>. This directive can be used to quickly create elegant typeaheads with any form text input. </p>
                            <p>It is very well integrated into AngularJS as it uses a subset of the
                                <a href="http://docs.angularjs.org/api/ng.directive:select">select directive</a> syntax, which is very flexible. Supported expressions are: </p>
                            <ul>
                                <li>
                                    <em>label</em> for
                                    <em>value</em> in
                                    <em>sourceArray</em>
                                </li>
                                <li>
                                    <em>select</em> as
                                    <em>label</em> for
                                    <em>value</em> in
                                    <em>sourceArray</em>
                                </li>
                            </ul>
                            <p>The <code>sourceArray</code> expression can use a special <code>$viewValue</code> variable that corresponds to the value entered inside the input.</p>
                            <p>This directive works with promises, meaning you can retrieve matches using the <code>$http</code> service with minimal effort.</p>
                            <p>The typeahead directives provide several attributes:</p>
                            <ul>
                                <li>
                                    <p><code>ng-model</code>
                                        <i class="glyphicon glyphicon-eye-open"></i> : Assignable angular expression to data-bind to </p>
                                </li>
                                <li>
                                    <p><code>typeahead</code>
                                        <i class="glyphicon glyphicon-eye-open"></i> : Comprehension Angular expression (see
                                        <a href="http://docs.angularjs.org/api/ng.directive:select">select directive</a>) </p>
                                </li>
                                <li>
                                    <p><code>typeahead-append-to-body</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: false)</em> : Should the typeahead popup be appended to $body instead of the parent element? </p>
                                </li>
                                <li>
                                    <p><code>typeahead-editable</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: true)</em> : Should it restrict model values to the ones selected from the popup only ? </p>
                                </li>
                                <li>
                                    <p><code>typeahead-input-formatter</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: undefined)</em> : Format the ng-model result after selection </p>
                                </li>
                                <li>
                                    <p><code>typeahead-loading</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: angular.noop)</em> : Binding to a variable that indicates if matches are being retrieved asynchronously </p>
                                </li>
                                <li>
                                    <p><code>typeahead-min-length</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: 1)</em> : Minimal no of characters that needs to be entered before typeahead kicks-in </p>
                                </li>
                                <li>
                                    <p><code>typeahead-on-select($item, $model, $label)</code>
                                        <em>(Defaults: null)</em> : A callback executed when a match is selected </p>
                                </li>
                                <li>
                                    <p><code>typeahead-template-url</code>
                                        <i class="glyphicon glyphicon-eye-open"></i> : Set custom item template </p>
                                </li>
                                <li>
                                    <p><code>typeahead-wait-ms</code>
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <em>(Defaults: 0)</em> : Minimal wait time after last character typed before typeahead kicks-in </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END RATING DEMO -->
        <!-- BEGIN RATING DEMO -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp">
                    <i class="icon-settings font-green-sharp"></i>
                    <span class="caption-subject bold uppercase">Rating</span>
                    <span class="caption-helper">ui.bootstrap.rating</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse"> </a>
                    <a href="#portlet-config" data-toggle="modal" class="config"> </a>
                    <a href="" class="reload"> </a>
                    <a href="" class="fullscreen"> </a>
                    <a href="" class="remove"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div ng-controller="RatingDemoCtrl">
                            <h4>Default</h4>
                            <uib-rating ng-model="rate" max="max" read-only="isReadonly" on-hover="hoveringOver(value)" on-leave="overStar = null" titles="['one','two','three']" aria-labelledby="default-rating"></uib-rating>
                            <span class="label" ng-class="{'label-warning': percent<30, 'label-info': percent>=30 && percent<70, 'label-success': percent>=70}" ng-show="overStar && !isReadonly">{{percent}}%</span> <pre style="margin:15px 0;">Rate: <b>{{rate}}</b> - Readonly is: <i>{{isReadonly}}</i> - Hovering over: <b>{{overStar || "none"}}</b></pre>
                            <button type="button" class="btn btn-sm btn-danger" ng-click="rate = 0"
                                ng-disabled="isReadonly">Clear</button>
                            <button type="button" class="btn btn-sm btn-default" ng-click="isReadonly = ! isReadonly">Toggle Readonly</button>
                            <hr />
                            <h4>Custom icons</h4>
                            <div ng-init="x = 5">
                                <uib-rating ng-model="x" max="15" state-on="'glyphicon-ok-sign'" state-off="'glyphicon-ok-circle'" aria-labelledby="custom-icons-1"></uib-rating>
                                <b>(
                                    <i>Rate:</i> {{x}})</b>
                            </div>
                            <div ng-init="y = 2">
                                <uib-rating ng-model="y" rating-states="ratingStates" aria-labelledby="custom-icons-2"></uib-rating>
                                <b>(
                                    <i>Rate:</i> {{y}})</b>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Rating directive that will take care of visualising a star rating bar.</p>
                        <h3>Settings</h3>
                        <h4><code>&lt;rating&gt;</code></h4>
                        <ul>
                            <li>
                                <p><code>ng-model</code>
                                    <i class="glyphicon glyphicon-eye-open"></i> : The current rate. </p>
                            </li>
                            <li>
                                <p><code>max</code>
                                    <em>(Defaults: 5)</em> : Changes the number of icons. </p>
                            </li>
                            <li>
                                <p><code>readonly</code>
                                    <i class="icon-eye-open"></i>
                                    <em>(Defaults: false)</em> : Prevent user's interaction. </p>
                            </li>
                            <li>
                                <p><code>on-hover(value)</code> : An optional expression called when user's mouse is over a particular icon.</p>
                            </li>
                            <li>
                                <p><code>on-leave()</code> : An optional expression called when user's mouse leaves the control altogether.</p>
                            </li>
                            <li>
                                <p><code>state-on</code>
                                    <em>(Defaults: null)</em> : A variable used in template to specify the state (class, src, etc) for selected icons. </p>
                            </li>
                            <li>
                                <p><code>state-off</code>
                                    <em>(Defaults: null)</em> : A variable used in template to specify the state for unselected icons. </p>
                            </li>
                            <li>
                                <p><code>rating-states</code>
                                    <em>(Defaults: null)</em> : An array of objects defining properties for all icons. In default template, <code>stateOn</code> &amp; <code>stateOff</code> property is used to specify the icon's class. </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END RATING DEMO -->
    </div>
</div>
<!-- END MAIN CONTENT -->
<!-- BEGIN MAIN JS -->
<script>
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function AccordionDemoCtrl($scope)
    {
        /** @type {boolean} */
        $scope.oneAtATime = true;
        /** @type {Array} */
        $scope.groups = [
        {
            title: "Dynamic Group Header - 1",
            content: "Dynamic Group Body - 1"
        },
        {
            title: "Dynamic Group Header - 2",
            content: "Dynamic Group Body - 2"
        }];
        /** @type {Array} */
        $scope.items = ["Item 1", "Item 2", "Item 3"];
        /**
         * @return {undefined}
         */
        $scope.addItem = function()
        {
            var vvar = $scope.items.length + 1;
            $scope.items.push("Item " + vvar);
        };
        $scope.status = {
            isCustomHeaderOpen: false,
            isFirstOpen: true,
            isFirstDisabled: false
        };
    }
    /**
     * @param {?} $scope
     * @return {undefined}
     */
    function AlertDemoCtrl($scope)
    {
        /** @type {Array} */
        $scope.alerts = [
        {
            type: "danger",
            msg: "Oh snap! Change a few things up and try submitting again."
        },
        {
            type: "success",
            msg: "Well done! You successfully read this important alert message."
        }];
        /**
         * @return {undefined}
         */
        $scope.addAlert = function()
        {
            $scope.alerts.push(
            {
                msg: "Another alert!"
            });
        };
        /**
         * @param {?} index
         * @return {undefined}
         */
        $scope.closeAlert = function(index)
        {
            $scope.alerts.splice(index, 1);
        };
    }
    /**
     * @param {?} $scope
     * @return {undefined}
     */
    function ButtonsCtrl($scope)
    {
        /** @type {number} */
        $scope.singleModel = 1;
        /** @type {string} */
        $scope.radioModel = "Middle";
        $scope.checkModel = {
            left: false,
            middle: true,
            right: false
        };
        /** @type {Array} */
        $scope.checkResults = [];
        $scope.$watchCollection("checkModel", function()
        {
            /** @type {Array} */
            $scope.checkResults = [];
            angular.forEach($scope.checkModel, function(dataAndEvents, spaceName)
            {
                if (dataAndEvents)
                {
                    $scope.checkResults.push(spaceName);
                }
            });
        });
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function TabsDemoCtrl($scope)
    {
        /** @type {Array} */
        $scope.tabs = [
        {
            title: "Dynamic Title 1",
            content: "Dynamic content 1"
        },
        {
            title: "Dynamic Title 2",
            content: "Dynamic content 2",
            disabled: true
        }];
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function CarouselDemoCtrl($scope)
    {
        /**
         * @param {Array} args
         * @return {undefined}
         */
        function render(args)
        {
            /** @type {number} */
            var i = 0;
            /** @type {number} */
            var valuesLen = values.length;
            for (; i < valuesLen; i++)
            {
                values[i].id = args.pop();
            }
        }
        /**
         * @return {?}
         */
        function compiler()
        {
            /** @type {Array} */
            var e = [];
            /** @type {number} */
            var n = 0;
            for (; n < l; ++n)
            {
                /** @type {number} */
                e[n] = n;
            }
            return next(e);
        }
        /**
         * @param {Array} result
         * @return {?}
         */
        function next(result)
        {
            var value;
            var key;
            var index = result.length;
            if (index)
            {
                for (; --index;)
                {
                    /** @type {number} */
                    key = Math.floor(Math.random() * (index + 1));
                    value = result[key];
                    result[key] = result[index];
                    result[index] = value;
                }
            }
            return result;
        }
        /** @type {number} */
        $scope.myInterval = 5E3;
        /** @type {boolean} */
        $scope.noWrapSlides = false;
        /** @type {number} */
        $scope.active = 0;
        /** @type {Array} */
        var values = $scope.slides = [];
        /** @type {number} */
        var l = 0;
        /**
         * @return {undefined}
         */
        $scope.addSlide = function()
        {
            /** @type {number} */
            var newWidth = 600 + values.length + 1;
            values.push(
            {
                image: "http://lorempixel.com/" + newWidth + "/300",
                text: ["Nice image", "Awesome photograph", "That is so cool", "I love that"][values.length % 4],
                id: l++
            });
        };
        /**
         * @return {undefined}
         */
        $scope.randomize = function()
        {
            var typePattern = compiler();
            render(typePattern);
        };
        /** @type {number} */
        var i = 0;
        for (; i < 4; i++)
        {
            $scope.addSlide();
        }
    }
    /**
     * @param {?} $scope
     * @return {undefined}
     */
    function CollapseDemoCtrl($scope)
    {
        $scope.isCollapsed = false;
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function DatepickerDemoCtrl($scope)
    {
        /**
         * @param {Object} settings
         * @return {?}
         */
        function process(settings)
        {
            var d = settings.date;
            var mode = settings.mode;
            return mode === "day" && (d.getDay() === 0 || d.getDay() === 6);
        }
        /**
         * @param {Object} options
         * @return {?}
         */
        function remove(options)
        {
            var date = options.date;
            var mode = options.mode;
            if (mode === "day")
            {
                var value = (new Date(date)).setHours(0, 0, 0, 0);
                /** @type {number} */
                var i = 0;
                for (; i < $scope.events.length; i++)
                {
                    var radio = (new Date($scope.events[i].date)).setHours(0, 0, 0, 0);
                    if (value === radio)
                    {
                        return $scope.events[i].status;
                    }
                }
            }
            return "";
        }
        /**
         * @return {undefined}
         */
        $scope.today = function()
        {
            /** @type {Date} */
            $scope.dt = new Date;
        };
        $scope.today();
        /**
         * @return {undefined}
         */
        $scope.clear = function()
        {
            /** @type {null} */
            $scope.dt = null;
        };
        $scope.options = {
            /** @type {function (Object): ?} */
            customClass: remove,
            minDate: new Date,
            showWeeks: true
        };
        /**
         * @return {undefined}
         */
        $scope.toggleMin = function()
        {
            /** @type {(Date|null)} */
            $scope.options.minDate = $scope.options.minDate ? null : new Date;
        };
        $scope.toggleMin();
        /**
         * @param {number} dt
         * @param {number} month
         * @param {string} d
         * @return {undefined}
         */
        $scope.setDate = function(dt, month, d)
        {
            /** @type {Date} */
            $scope.dt = new Date(dt, month, d);
        };
        /** @type {Date} */
        var newDate = new Date;
        newDate.setDate(newDate.getDate() + 1);
        /** @type {Date} */
        var now = new Date(newDate);
        now.setDate(newDate.getDate() + 1);
        /** @type {Array} */
        $scope.events = [
        {
            date: newDate,
            status: "full"
        },
        {
            date: now,
            status: "partially"
        }];
    }

    function DatepickerPopupDemoCtrl($scope)
    {
        $scope.today = function()
        {
            $scope.dt = new Date();
        };
        $scope.today();
        $scope.clear = function()
        {
            $scope.dt = null;
        };
        $scope.inlineOptions = {
            customClass: getDayClass,
            minDate: new Date(),
            showWeeks: true
        };
        $scope.dateOptions = {
            dateDisabled: disabled,
            formatYear: 'yy',
            maxDate: new Date(2020, 5, 22),
            minDate: new Date(),
            startingDay: 1
        };
        // Disable weekend selection
        function disabled(data)
        {
            var date = data.date,
                mode = data.mode;
            return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
        }
        $scope.toggleMin = function()
        {
            $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
            $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
        };
        $scope.toggleMin();
        $scope.open1 = function()
        {
            $scope.popup1.opened = true;
        };
        $scope.open2 = function()
        {
            $scope.popup2.opened = true;
        };
        $scope.setDate = function(year, month, day)
        {
            $scope.dt = new Date(year, month, day);
        };
        $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
        $scope.format = $scope.formats[0];
        $scope.altInputFormats = ['M!/d!/yyyy'];
        $scope.popup1 = {
            opened: false
        };
        $scope.popup2 = {
            opened: false
        };
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        var afterTomorrow = new Date();
        afterTomorrow.setDate(tomorrow.getDate() + 1);
        $scope.events = [
        {
            date: tomorrow,
            status: 'full'
        },
        {
            date: afterTomorrow,
            status: 'partially'
        }];

        function getDayClass(data)
        {
            var date = data.date,
                mode = data.mode;
            if (mode === 'day')
            {
                var dayToCheck = new Date(date).setHours(0, 0, 0, 0);
                for (var i = 0; i < $scope.events.length; i++)
                {
                    var currentDay = new Date($scope.events[i].date).setHours(0, 0, 0, 0);
                    if (dayToCheck === currentDay)
                    {
                        return $scope.events[i].status;
                    }
                }
            }
            return '';
        }
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function TimepickerDemoCtrl($scope)
    {
        /** @type {Date} */
        $scope.mytime = new Date;
        /** @type {number} */
        $scope.hstep = 1;
        /** @type {number} */
        $scope.mstep = 15;
        $scope.options = {
            hstep: [1, 2, 3],
            mstep: [1, 5, 10, 15, 25, 30]
        };
        /** @type {boolean} */
        $scope.ismeridian = true;
        /**
         * @return {undefined}
         */
        $scope.toggleMode = function()
        {
            /** @type {boolean} */
            $scope.ismeridian = !$scope.ismeridian;
        };
        /**
         * @return {undefined}
         */
        $scope.update = function()
        {
            /** @type {Date} */
            var d = new Date;
            d.setHours(14);
            d.setMinutes(0);
            /** @type {Date} */
            $scope.mytime = d;
        };
        /**
         * @return {undefined}
         */
        $scope.changed = function()
        {
            $log.log("Time changed to: " + $scope.mytime);
        };
        /**
         * @return {undefined}
         */
        $scope.clear = function()
        {
            /** @type {null} */
            $scope.mytime = null;
        };
    }
    /**
     * @param {Object} settings
     * @return {undefined}
     */
    function DropdownCtrl($scope, $log)
    {
        $scope.items = ['The first choice!', 'And another choice for you.', 'but wait! A third!'];
        $scope.status = {
            isopen: false
        };
        $scope.toggled = function(open)
        {
            $log.log('Dropdown is now: ', open);
        };
        $scope.toggleDropdown = function($event)
        {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.status.isopen = !$scope.status.isopen;
        };
        $scope.appendToEl = angular.element(document.querySelector('#dropdown-long-content'));
    }
    /**
     * @param {Object} $scope
     * @param {?} dataAndEvents
     * @param {?} $log
     * @return {undefined}
     */
    function ModalDemoCtrl($scope, $uibModal, $log)
    {
        /** @type {Array} */
        $scope.items = ["item1", "item2", "item3"];
        /** @type {boolean} */
        $scope.animationsEnabled = true;
        /**
         * @param {number} opt_attributes
         * @return {undefined}
         */
        $scope.open = function(opt_attributes)
        {
            var out = $uibModal.open(
            {
                animation: $scope.animationsEnabled,
                templateUrl: "myModalContent.html",
                controller: "ModalInstanceCtrl",
                size: opt_attributes,
                resolve:
                {
                    /**
                     * @return {?}
                     */
                    items: function()
                    {
                        return $scope.items;
                    }
                }
            });
            out.result.then(function(value)
            {
                $scope.selected = value;
            }, function()
            {
                $log.info("Modal dismissed at: " + new Date);
            });
        };
        /**
         * @return {undefined}
         */
        $scope.toggleAnimation = function()
        {
            /** @type {boolean} */
            $scope.animationsEnabled = !$scope.animationsEnabled;
        };
    }
    /**
     * @param {Object} $scope
     * @param {?} dataAndEvents
     * @param {Array} items
     * @return {undefined}
     */
    function ModalInstanceCtrl($scope, $uibModalInstance, items)
    {
        $scope.items = items;
        $scope.selected = {
            item: $scope.items[0]
        };
        $scope.ok = function()
        {
            $uibModalInstance.close($scope.selected.item);
        };
        $scope.cancel = function()
        {
            $uibModalInstance.dismiss('cancel');
        };
    }
    /**
     * @param {string} $scope
     * @return {undefined}
     */
    function PaginationDemoCtrl($scope, $log)
    {
        /** @type {number} */
        $scope.totalItems = 64;
        /** @type {number} */
        $scope.currentPage = 4;
        /**
         * @param {number} pageNo
         * @return {undefined}
         */
        $scope.setPage = function(pageNo)
        {
            /** @type {number} */
            $scope.currentPage = pageNo;
        };
        /**
         * @return {undefined}
         */
        $scope.pageChanged = function()
        {
            $log.log("Page changed to: " + $scope.currentPage);
        };
        /** @type {number} */
        $scope.maxSize = 5;
        /** @type {number} */
        $scope.bigTotalItems = 175;
        /** @type {number} */
        $scope.bigCurrentPage = 1;
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function PopoverDemoCtrl($scope, $sce)
    {
        $scope.dynamicPopover = {
            content: "Hello, World!",
            templateUrl: "myPopoverTemplate.html",
            title: "Title"
        };
        $scope.placement = {
            options: ["top", "top-left", "top-right", "bottom", "bottom-left", "bottom-right", "left", "left-top", "left-bottom", "right", "right-top", "right-bottom"],
            selected: "top"
        };
        $scope.htmlPopover = $sce.trustAsHtml('<b style="color: red">I can</b> have <div class="label label-success">HTML</div> content');
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function TooltipDemoCtrl($scope, $sce)
    {
        /** @type {string} */
        $scope.dynamicTooltip = "Hello, World!";
        /** @type {string} */
        $scope.dynamicTooltipText = "dynamic";
        $scope.htmlTooltip = $sce.trustAsHtml("I've been made <b>bold</b>!");
        $scope.placement = {
            options: ["top", "top-left", "top-right", "bottom", "bottom-left", "bottom-right", "left", "left-top", "left-bottom", "right", "right-top", "right-bottom"],
            selected: "top"
        };
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function ProgressDemoCtrl($scope)
    {
        /** @type {number} */
        $scope.max = 200;
        /**
         * @return {undefined}
         */
        $scope.random = function()
        {
            /** @type {number} */
            var dynamic = Math.floor(Math.random() * 100 + 1);
            var type;
            if (dynamic < 25)
            {
                /** @type {string} */
                type = "success";
            }
            else
            {
                if (dynamic < 50)
                {
                    /** @type {string} */
                    type = "info";
                }
                else
                {
                    if (dynamic < 75)
                    {
                        /** @type {string} */
                        type = "warning";
                    }
                    else
                    {
                        /** @type {string} */
                        type = "danger";
                    }
                }
            }
            /** @type {boolean} */
            $scope.showWarning = type === "danger" || type === "warning";
            /** @type {number} */
            $scope.dynamic = dynamic;
            /** @type {string} */
            $scope.type = type;
        };
        $scope.random();
        /**
         * @return {undefined}
         */
        $scope.randomStacked = function()
        {
            /** @type {Array} */
            $scope.stacked = [];
            /** @type {Array} */
            var types = ["success", "info", "warning", "danger"];
            /** @type {number} */
            var i = 0;
            /** @type {number} */
            var padLength = Math.floor(Math.random() * 4 + 1);
            for (; i < padLength; i++)
            {
                /** @type {number} */
                var type = Math.floor(Math.random() * 4);
                $scope.stacked.push(
                {
                    value: Math.floor(Math.random() * 30 + 1),
                    type: types[type]
                });
            }
        };
        $scope.randomStacked();
    }
    /**
     * @param {Node} $scope
     * @return {undefined}
     */
    function TypeaheadCtrl($scope, $http)
    {
        var text;
        $scope.selected = undefined;
        /** @type {Array} */
        $scope.states = ["Alabama", "Alaska", "Arizona", "Arkansas", "California", "Colorado", "Connecticut", "Delaware", "Florida", "Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas", "Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire", "New Jersey", "New Mexico", "New York", "North Dakota", "North Carolina", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"];
        /**
         * @param {string} url
         * @return {?}
         */
        $scope.getLocation = function(url)
        {
            return $http.get("//maps.googleapis.com/maps/api/geocode/json",
            {
                params:
                {
                    address: url,
                    sensor: false
                }
            }).then(function(e)
            {
                return e.data.results.map(function(item)
                {
                    return item.formatted_address;
                });
            });
        };
        /**
         * @param {?} textAlt
         * @return {?}
         */
        $scope.ngModelOptionsSelected = function(textAlt)
        {
            if (arguments.length)
            {
                text = textAlt;
            }
            else
            {
                return text;
            }
        };
        $scope.modelOptions = {
            debounce:
            {
                default: 500,
                blur: 250
            },
            getterSetter: true
        };
        /** @type {Array} */
        $scope.statesWithFlags = [
        {
            "name": "Alabama",
            "flag": "5/5c/Flag_of_Alabama.svg/45px-Flag_of_Alabama.svg.png"
        },
        {
            "name": "Alaska",
            "flag": "e/e6/Flag_of_Alaska.svg/43px-Flag_of_Alaska.svg.png"
        },
        {
            "name": "Arizona",
            "flag": "9/9d/Flag_of_Arizona.svg/45px-Flag_of_Arizona.svg.png"
        },
        {
            "name": "Arkansas",
            "flag": "9/9d/Flag_of_Arkansas.svg/45px-Flag_of_Arkansas.svg.png"
        },
        {
            "name": "California",
            "flag": "0/01/Flag_of_California.svg/45px-Flag_of_California.svg.png"
        },
        {
            "name": "Colorado",
            "flag": "4/46/Flag_of_Colorado.svg/45px-Flag_of_Colorado.svg.png"
        },
        {
            "name": "Connecticut",
            "flag": "9/96/Flag_of_Connecticut.svg/39px-Flag_of_Connecticut.svg.png"
        },
        {
            "name": "Delaware",
            "flag": "c/c6/Flag_of_Delaware.svg/45px-Flag_of_Delaware.svg.png"
        },
        {
            "name": "Florida",
            "flag": "f/f7/Flag_of_Florida.svg/45px-Flag_of_Florida.svg.png"
        },
        {
            "name": "Georgia",
            "flag": "5/54/Flag_of_Georgia_%28U.S._state%29.svg/46px-Flag_of_Georgia_%28U.S._state%29.svg.png"
        },
        {
            "name": "Hawaii",
            "flag": "e/ef/Flag_of_Hawaii.svg/46px-Flag_of_Hawaii.svg.png"
        },
        {
            "name": "Idaho",
            "flag": "a/a4/Flag_of_Idaho.svg/38px-Flag_of_Idaho.svg.png"
        },
        {
            "name": "Illinois",
            "flag": "0/01/Flag_of_Illinois.svg/46px-Flag_of_Illinois.svg.png"
        },
        {
            "name": "Indiana",
            "flag": "a/ac/Flag_of_Indiana.svg/45px-Flag_of_Indiana.svg.png"
        },
        {
            "name": "Iowa",
            "flag": "a/aa/Flag_of_Iowa.svg/44px-Flag_of_Iowa.svg.png"
        },
        {
            "name": "Kansas",
            "flag": "d/da/Flag_of_Kansas.svg/46px-Flag_of_Kansas.svg.png"
        },
        {
            "name": "Kentucky",
            "flag": "8/8d/Flag_of_Kentucky.svg/46px-Flag_of_Kentucky.svg.png"
        },
        {
            "name": "Louisiana",
            "flag": "e/e0/Flag_of_Louisiana.svg/46px-Flag_of_Louisiana.svg.png"
        },
        {
            "name": "Maine",
            "flag": "3/35/Flag_of_Maine.svg/45px-Flag_of_Maine.svg.png"
        },
        {
            "name": "Maryland",
            "flag": "a/a0/Flag_of_Maryland.svg/45px-Flag_of_Maryland.svg.png"
        },
        {
            "name": "Massachusetts",
            "flag": "f/f2/Flag_of_Massachusetts.svg/46px-Flag_of_Massachusetts.svg.png"
        },
        {
            "name": "Michigan",
            "flag": "b/b5/Flag_of_Michigan.svg/45px-Flag_of_Michigan.svg.png"
        },
        {
            "name": "Minnesota",
            "flag": "b/b9/Flag_of_Minnesota.svg/46px-Flag_of_Minnesota.svg.png"
        },
        {
            "name": "Mississippi",
            "flag": "4/42/Flag_of_Mississippi.svg/45px-Flag_of_Mississippi.svg.png"
        },
        {
            "name": "Missouri",
            "flag": "5/5a/Flag_of_Missouri.svg/46px-Flag_of_Missouri.svg.png"
        },
        {
            "name": "Montana",
            "flag": "c/cb/Flag_of_Montana.svg/45px-Flag_of_Montana.svg.png"
        },
        {
            "name": "Nebraska",
            "flag": "4/4d/Flag_of_Nebraska.svg/46px-Flag_of_Nebraska.svg.png"
        },
        {
            "name": "Nevada",
            "flag": "f/f1/Flag_of_Nevada.svg/45px-Flag_of_Nevada.svg.png"
        },
        {
            "name": "New Hampshire",
            "flag": "2/28/Flag_of_New_Hampshire.svg/45px-Flag_of_New_Hampshire.svg.png"
        },
        {
            "name": "New Jersey",
            "flag": "9/92/Flag_of_New_Jersey.svg/45px-Flag_of_New_Jersey.svg.png"
        },
        {
            "name": "New Mexico",
            "flag": "c/c3/Flag_of_New_Mexico.svg/45px-Flag_of_New_Mexico.svg.png"
        },
        {
            "name": "New York",
            "flag": "1/1a/Flag_of_New_York.svg/46px-Flag_of_New_York.svg.png"
        },
        {
            "name": "North Carolina",
            "flag": "b/bb/Flag_of_North_Carolina.svg/45px-Flag_of_North_Carolina.svg.png"
        },
        {
            "name": "North Dakota",
            "flag": "e/ee/Flag_of_North_Dakota.svg/38px-Flag_of_North_Dakota.svg.png"
        },
        {
            "name": "Ohio",
            "flag": "4/4c/Flag_of_Ohio.svg/46px-Flag_of_Ohio.svg.png"
        },
        {
            "name": "Oklahoma",
            "flag": "6/6e/Flag_of_Oklahoma.svg/45px-Flag_of_Oklahoma.svg.png"
        },
        {
            "name": "Oregon",
            "flag": "b/b9/Flag_of_Oregon.svg/46px-Flag_of_Oregon.svg.png"
        },
        {
            "name": "Pennsylvania",
            "flag": "f/f7/Flag_of_Pennsylvania.svg/45px-Flag_of_Pennsylvania.svg.png"
        },
        {
            "name": "Rhode Island",
            "flag": "f/f3/Flag_of_Rhode_Island.svg/32px-Flag_of_Rhode_Island.svg.png"
        },
        {
            "name": "South Carolina",
            "flag": "6/69/Flag_of_South_Carolina.svg/45px-Flag_of_South_Carolina.svg.png"
        },
        {
            "name": "South Dakota",
            "flag": "1/1a/Flag_of_South_Dakota.svg/46px-Flag_of_South_Dakota.svg.png"
        },
        {
            "name": "Tennessee",
            "flag": "9/9e/Flag_of_Tennessee.svg/46px-Flag_of_Tennessee.svg.png"
        },
        {
            "name": "Texas",
            "flag": "f/f7/Flag_of_Texas.svg/45px-Flag_of_Texas.svg.png"
        },
        {
            "name": "Utah",
            "flag": "f/f6/Flag_of_Utah.svg/45px-Flag_of_Utah.svg.png"
        },
        {
            "name": "Vermont",
            "flag": "4/49/Flag_of_Vermont.svg/46px-Flag_of_Vermont.svg.png"
        },
        {
            "name": "Virginia",
            "flag": "4/47/Flag_of_Virginia.svg/44px-Flag_of_Virginia.svg.png"
        },
        {
            "name": "Washington",
            "flag": "5/54/Flag_of_Washington.svg/46px-Flag_of_Washington.svg.png"
        },
        {
            "name": "West Virginia",
            "flag": "2/22/Flag_of_West_Virginia.svg/46px-Flag_of_West_Virginia.svg.png"
        },
        {
            "name": "Wisconsin",
            "flag": "2/22/Flag_of_Wisconsin.svg/45px-Flag_of_Wisconsin.svg.png"
        },
        {
            "name": "Wyoming",
            "flag": "b/bc/Flag_of_Wyoming.svg/43px-Flag_of_Wyoming.svg.png"
        }];
    }
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    function RatingDemoCtrl($scope)
    {
        /** @type {number} */
        $scope.rate = 7;
        /** @type {number} */
        $scope.max = 10;
        /** @type {boolean} */
        $scope.isReadonly = false;
        /**
         * @param {?} value
         * @return {undefined}
         */
        $scope.hoveringOver = function(value)
        {
            $scope.overStar = value;
            /** @type {number} */
            $scope.percent = 100 * (value / $scope.max);
        };
        /** @type {Array} */
        $scope.ratingStates = [
        {
            stateOn: "glyphicon-ok-sign",
            stateOff: "glyphicon-ok-circle"
        },
        {
            stateOn: "glyphicon-star",
            stateOff: "glyphicon-star-empty"
        },
        {
            stateOn: "glyphicon-heart",
            stateOff: "glyphicon-ban-circle"
        },
        {
            stateOn: "glyphicon-heart"
        },
        {
            stateOff: "glyphicon-off"
        }];
    }
</script>
<!-- END MAIN JS -->