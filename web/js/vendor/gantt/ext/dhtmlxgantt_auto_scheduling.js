/*
@license

dhtmlxGantt v.6.0.2 Professional Evaluation
This software is covered by DHTMLX Evaluation License. Contact sales@dhtmlx.com to get Commercial or Enterprise license. Usage without proper license is prohibited.

(c) Dinamenta, UAB.

*/
Gantt.plugin(function(t){!function(t){var e={};function n(r){if(e[r])return e[r].exports;var a=e[r]={i:r,l:!1,exports:{}};return t[r].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var a in t)n.d(r,a,function(e){return t[e]}.bind(null,a));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/codebase/",n(n.s=221)}({18:function(t,e,n){var r=n(3);t.exports=function(){return{getVertices:function(t){for(var e,n={},r=0,a=t.length;r<a;r++)n[(e=t[r]).target]=e.target,n[e.source]=e.source;var i,o=[];for(var r in n)i=n[r],o.push(i);return o},topologicalSort:function(t){for(var e=this.getVertices(t),n={},r=0,a=e.length;r<a;r++)n[e[r]]={id:e[r],$source:[],$target:[],$incoming:0};for(r=0,a=t.length;r<a;r++){var i=n[t[r].target];i.$target.push(r),i.$incoming=i.$target.length,n[t[r].source].$source.push(r)}for(var o=e.filter(function(t){return!n[t].$incoming}),s=[];o.length;){var u=o.pop();s.push(u);var c=n[u];for(r=0;r<c.$source.length;r++){var l=n[t[c.$source[r]].target];l.$incoming--,l.$incoming||o.push(l.id)}}return s},groupAdjacentEdges:function(t){for(var e,n={},r=0,a=t.length;r<a;r++)n[(e=t[r]).source]||(n[e.source]=[]),n[e.source].push(e);return n},tarjanStronglyConnectedComponents:function(t,e){for(var n={},r=[],a=this.groupAdjacentEdges(e),i=!1,o=[],s=0;s<t.length;s++){var u=_(t[s]);if(!u.visited)for(var c=[u],l=0;c.length;){var d=c.pop();d.visited||(d.index=l,d.lowLink=l,l++,r.push(d),d.onStack=!0,d.visited=!0),i=!1;e=a[d.id]||[];for(var g=0;g<e.length;g++){var f=_(e[g].target);if(f.edge=e[g],void 0===f.index){c.push(d),c.push(f),i=!0;break}f.onStack&&(d.lowLink=Math.min(d.lowLink,f.index))}if(!i){if(d.index==d.lowLink){for(var h={tasks:[],links:[]};(f=r.pop()).onStack=!1,h.tasks.push(f.id),f.edge&&h.links.push(f.edge.id),f!=d;);o.push(h)}c.length&&(f=d,(d=c[c.length-1]).lowLink=Math.min(d.lowLink,f.lowLink))}}}return o;function _(t){return n[t]||(n[t]={id:t,onStack:!1,index:void 0,lowLink:void 0,edge:void 0}),n[t]}},findLoops:function(t){var e=[];r.forEach(t,function(t){t.target==t.source&&e.push([t.target,t.source])});var n=this.getVertices(t),a=this.tarjanStronglyConnectedComponents(n,t);return r.forEach(a,function(t){t.tasks.length>1&&e.push(t)}),e}}}},19:function(t,e){t.exports=function(t){t._get_linked_task=function(e,n){var r=null,a=n?e.target:e.source;return t.isTaskExists(a)&&(r=t.getTask(a)),r},t._get_link_target=function(e){return t._get_linked_task(e,!0)},t._get_link_source=function(e){return t._get_linked_task(e,!1)};var e=!1,n={},r={},a={},i={};t._isLinksCacheEnabled=function(){return e},t._startLinksCache=function(){n={},r={},a={},i={},e=!0},t._endLinksCache=function(){n={},r={},a={},i={},e=!1},t._formatLink=function(r){if(e&&n[r.id])return n[r.id];var a=[],i=this._get_link_target(r),o=this._get_link_source(r);if(!o||!i)return a;if(t.isSummaryTask(i)&&t.isChildOf(o.id,i.id)||t.isSummaryTask(o)&&t.isChildOf(i.id,o.id))return a;for(var s=this._getImplicitLinks(r,o,function(t){return 0},!0),u=t.config.auto_scheduling_move_projects,c=this.isSummaryTask(i)?this.getSubtaskDates(i.id):{start_date:i.start_date,end_date:i.end_date},l=this._getImplicitLinks(r,i,function(e){return u?e.$target.length||t.getState().drag_id==e.id?0:t.calculateDuration({start_date:c.start_date,end_date:e.start_date,task:o}):0}),d=0,g=s.length;d<g;d++)for(var f=s[d],h=0,_=l.length;h<_;h++){var v=l[h],k=1*f.lag+1*v.lag,p={id:r.id,type:r.type,source:f.task,target:v.task,lag:(1*r.lag||0)+k};a.push(t._convertToFinishToStartLink(v.task,p,o,i,f.taskParent,v.taskParent))}return e&&(n[r.id]=a),a},t._isAutoSchedulable=function(t){return!1!==t.auto_scheduling},t._getImplicitLinks=function(e,n,r,a){var i=[];if(this.isSummaryTask(n)){var o,s={};for(var u in this.eachTask(function(t){this.isSummaryTask(t)||(s[t.id]=t)},n.id),s){var c=s[u],l=a?c.$source:c.$target;o=!1;for(var d=0;d<l.length;d++){var g=t.getLink(l[d]),f=a?g.target:g.source,h=s[f];if(h&&!1!==c.auto_scheduling&&!1!==h.auto_scheduling&&(g.target==h.id&&Math.abs(g.lag)<=h.duration||g.target==c.id&&Math.abs(g.lag)<=c.duration)){o=!0;break}}o||i.push({task:c.id,taskParent:c.parent,lag:r(c)})}}else i.push({task:n.id,taskParent:n.parent,lag:0});return i},t._getDirectDependencies=function(t,e){for(var n=[],r=[],a=e?t.$source:t.$target,i=0;i<a.length;i++){var o=this.getLink(a[i]);if(this.isTaskExists(o.source)&&this.isTaskExists(o.target)){var s=this.getTask(o.target);this._isAutoSchedulable(s)&&n.push(this.getLink(a[i]))}}for(i=0;i<n.length;i++)r=r.concat(this._formatLink(n[i]));return r},t._getInheritedDependencies=function(t,n){var i,o=!1,s=[];return this.isTaskExists(t.id)&&this.eachParent(function(t){var u;o||(e&&(i=n?r:a)[t.id]?s=s.concat(i[t.id]):this.isSummaryTask(t)&&(this._isAutoSchedulable(t)?(u=this._getDirectDependencies(t,n),e&&(i[t.id]=u),s=s.concat(u)):o=!0))},t.id,this),s},t._getDirectSuccessors=function(t){return this._getDirectDependencies(t,!0)},t._getInheritedSuccessors=function(t){return this._getInheritedDependencies(t,!0)},t._getDirectPredecessors=function(t){return this._getDirectDependencies(t,!1)},t._getInheritedPredecessors=function(t){return this._getInheritedDependencies(t,!1)},t._getSuccessors=function(t,e){var n=this._getDirectSuccessors(t);return e?n:n.concat(this._getInheritedSuccessors(t))},t._getPredecessors=function(t,n){var r,a=t.id+n;if(e&&i[a])return i[a];var o=this._getDirectPredecessors(t);return r=n?o:o.concat(this._getInheritedPredecessors(t)),e&&(i[a]=r),r},t._convertToFinishToStartLink=function(e,n,r,a,i,o){var s={target:e,link:t.config.links.finish_to_start,id:n.id,lag:n.lag||0,source:n.source,preferredStart:null,sourceParent:i,targetParent:o,hashSum:null},u=0;switch(n.type){case t.config.links.start_to_start:u=-r.duration;break;case t.config.links.finish_to_finish:u=-a.duration;break;case t.config.links.start_to_finish:u=-r.duration-a.duration;break;default:u=0}return s.lag+=u,s.hashSum=s.lag+"_"+s.link+"_"+s.source+"_"+s.target,s}}},221:function(e,n,r){r(19)(t);var a=r(9)(t),i=r(18)(t);t.config.auto_scheduling=!1,t.config.auto_scheduling_descendant_links=!1,t.config.auto_scheduling_initial=!0,t.config.auto_scheduling_strict=!1,t.config.auto_scheduling_move_projects=!0,function(){function e(t,e,n){for(var r,a=[t],i=[],o={};a.length>0;)if(!n[r=a.shift()]){n[r]=!0,i.push(r);for(var s=0;s<e.length;s++){var u=e[s];u.source!=r||n[u.target]?u.target!=r||n[u.source]||(a.push(u.source),o[u.id]=!0,e.splice(s,1),s--):(a.push(u.target),o[u.id]=!0,e.splice(s,1),s--)}}var c=[];for(var s in o)c.push(s);return{tasks:i,links:c}}t._autoSchedulingDateResolver={isFirstSmaller:function(e,n,r){return!!(e.valueOf()<n.valueOf()&&t._hasDuration(e,n,r))},isSmallerOrDefault:function(t,e,n){return!(t&&!this.isFirstSmaller(t,e,n))},resolveRelationDate:function(e,n,r){for(var a,i=null,o=null,s=null,u=0;u<n.length;u++){var c=n[u];e=c.target,s=c.preferredStart,a=t.getTask(e);var l=this.getConstraintDate(c,r,a);this.isSmallerOrDefault(s,l,a)&&this.isSmallerOrDefault(i,l,a)&&(i=l,o=c.id)}return i&&(i=t.getClosestWorkTime({date:i,dir:"future",task:t.getTask(e)})),{link:o,task:e,start_date:i}},getConstraintDate:function(e,n,r){var a=n(e.source),i=r,o=t.getClosestWorkTime({date:a,dir:"future",task:i});return a&&e.lag&&1*e.lag==e.lag&&(o=t.calculateEndDate({start_date:a,duration:1*e.lag,task:i})),o}},t._autoSchedulingPlanner={generatePlan:function(e){for(var n,r,a=i.topologicalSort(e),o={},s={},u=0,c=a.length;u<c;u++){n=a[u],!1!==(v=t.getTask(n)).auto_scheduling&&(o[n]=[],s[n]=null)}function l(e){var n=s[e],r=t.getTask(e);return n&&(n.start_date||n.end_date)?n.end_date?n.end_date:t.calculateEndDate({start_date:n.start_date,duration:r.duration,task:r}):r.end_date}for(u=0,c=e.length;u<c;u++)o[(r=e[u]).target]&&o[r.target].push(r);var d=t._autoSchedulingDateResolver,g=[];for(u=0;u<a.length;u++){var f=a[u],h=d.resolveRelationDate(f,o[f]||[],l);if(h.start_date&&t.isLinkExists(h.link)){var _=t.getLink(h.link),v=t.getTask(f),k=t.getTask(_.source);if(v.start_date.valueOf()!==h.start_date.valueOf()&&!1===t.callEvent("onBeforeTaskAutoSchedule",[v,h.start_date,_,k]))continue}s[f]=h,h.start_date&&g.push(h)}return g},applyProjectPlan:function(e){for(var n,r,a,i,o=[],s=0;s<e.length;s++)if(a=null,i=null,(n=e[s]).task){r=t.getTask(n.task),n.link&&(a=t.getLink(n.link),i=t.getTask(a.source));var u=null;n.start_date&&r.start_date.valueOf()!=n.start_date.valueOf()&&(u=n.start_date),u&&(r.start_date=u,r.end_date=t.calculateEndDate(r),o.push(r.id),t.callEvent("onAfterTaskAutoSchedule",[r,u,a,i]))}return o}},t._autoSchedulingPreferredDates=function(e,n){for(var r=0;r<n.length;r++){var a=n[r],i=t.getTask(a.target);t.config.auto_scheduling_strict&&a.target!=e||(a.preferredStart=new Date(i.start_date))}},t._autoSchedule=function(e,n,r){if(!1!==t.callEvent("onBeforeAutoSchedule",[e])){t._autoscheduling_in_progress=!0;var a=[],o=i.findLoops(n);if(o.length)t.callEvent("onAutoScheduleCircularLink",[o]);else{var s=t._autoSchedulingPlanner;t._autoSchedulingPreferredDates(e,n);var u=s.generatePlan(n);a=s.applyProjectPlan(u),r&&r(a)}t._autoscheduling_in_progress=!1,t.callEvent("onAfterAutoSchedule",[e,a])}},t.autoSchedule=function(e,n){n=void 0===n||!!n;var r=a.getLinkedTasks(e,n);t._autoSchedule(e,r,t._finalizeAutoSchedulingChanges)},t._finalizeAutoSchedulingChanges=function(e){var n=!1;function r(){for(var n=0;n<e.length;n++)t.updateTask(e[n])}1==e.length?t.eachParent(function e(r){if(!n){var a=r.start_date.valueOf(),i=r.end_date.valueOf();if(t.resetProjectDates(r),r.start_date.valueOf()==a&&r.end_date.valueOf()==i)for(var o=t.getChildren(r.id),s=0;!n&&s<o.length;s++)e(t.getTask(o[s]));else n=!0}},e[0]):e.length&&(n=!0),n?t.batchUpdate(r):r()},t.isCircularLink=function(e){return!!t._getConnectedGroup(e)},t._getConnectedGroup=function(e){var n=a.getLinkedTasks();t.isLinkExists(e.id)||(n=n.concat(t._formatLink(e)));for(var r=i.findLoops(n),o=0;o<r.length;o++)for(var s=r[o].links,u=0;u<s.length;u++)if(s[u]==e.id)return r[o];return null},t.findCycles=function(){var t=a.getLinkedTasks();return i.findLoops(t)},t._attachAutoSchedulingHandlers=function(){var e,n;t._autoScheduleAfterLinkChange=function(e,n){t.config.auto_scheduling&&!this._autoscheduling_in_progress&&t.autoSchedule(n.source)},t.attachEvent("onAfterLinkUpdate",t._autoScheduleAfterLinkChange),t.attachEvent("onAfterLinkAdd",t._autoScheduleAfterLinkChange),t.attachEvent("onAfterLinkDelete",function(t,e){if(this.config.auto_scheduling&&!this._autoscheduling_in_progress&&this.isTaskExists(e.target)){var n=this.getTask(e.target),r=this._getPredecessors(n);r.length&&this.autoSchedule(r[0].source,!1)}}),t.attachEvent("onParse",function(){t.config.auto_scheduling&&t.config.auto_scheduling_initial&&t.autoSchedule()}),t._preventCircularLink=function(e,n){return!t.isCircularLink(n)||(t.callEvent("onCircularLinkError",[n,t._getConnectedGroup(n)]),!1)},t._preventDescendantLink=function(e,n){var r=t.getTask(n.source),a=t.getTask(n.target);return!(!t.config.auto_scheduling_descendant_links&&(t.isChildOf(r.id,a.id)&&t.isSummaryTask(a)||t.isChildOf(a.id,r.id)&&t.isSummaryTask(r)))},t.attachEvent("onBeforeLinkAdd",t._preventCircularLink),t.attachEvent("onBeforeLinkAdd",t._preventDescendantLink),t.attachEvent("onBeforeLinkUpdate",t._preventCircularLink),t.attachEvent("onBeforeLinkUpdate",t._preventDescendantLink),t._datesNotEqual=function(t,e,n,r){return t.valueOf()>e.valueOf()?this._hasDuration({start_date:e,end_date:t,task:r}):this._hasDuration({start_date:t,end_date:e,task:n})},t._notEqualTaskDates=function(e,n){return!!this._datesNotEqual(e.start_date,n.start_date,e,n)||(!(!this._datesNotEqual(e.start_date,n.start_date,e,n)&&(!this._datesNotEqual(e.end_date,n.end_date,e,n)&&e.duration==n.duration||e.type==t.config.types.milestone))||void 0)},t.attachEvent("onBeforeTaskDrag",function(r,i,o){return t.config.auto_scheduling&&t.config.auto_scheduling_move_projects&&(e=a.getLinkedTasks(r,!0),n=r),!0}),t._autoScheduleAfterDND=function(r,i){if(t.config.auto_scheduling&&!this._autoscheduling_in_progress){var o=this.getTask(r);t._notEqualTaskDates(i,o)&&(t.config.auto_scheduling_move_projects&&n==r?(t.calculateDuration(i)!=t.calculateDuration(o)&&function(e,n){for(var r=!1,i=0;i<n.length;i++){var o=t.getLink(n[i].id);!o||o.type!=t.config.links.start_to_start&&o.type!=t.config.links.start_to_finish||(n.splice(i,1),i--,r=!0)}if(r){var s={};for(i=0;i<n.length;i++)s[n[i].id]=!0;var u=a.getLinkedTasks(e,!0);for(i=0;i<u.length;i++)s[u[i].id]||n.push(u[i])}}(r,e),t._autoSchedule(r,e,t._finalizeAutoSchedulingChanges)):t.autoSchedule(o.id))}return e=null,n=null,!0},t._lightBoxChangesHandler=function(e,n){if(t.config.auto_scheduling&&!this._autoscheduling_in_progress){var r=this.getTask(e);t._notEqualTaskDates(n,r)&&(t._autoschedule_lightbox_id=e)}return!0},t._lightBoxSaveHandler=function(e,n){return t.config.auto_scheduling&&!this._autoscheduling_in_progress&&t._autoschedule_lightbox_id&&t._autoschedule_lightbox_id==e&&(t._autoschedule_lightbox_id=null,t.autoSchedule(n.id)),!0},t.attachEvent("onBeforeTaskChanged",function(e,n,r){return t._autoScheduleAfterDND(e,r)}),t.attachEvent("onLightboxSave",t._lightBoxChangesHandler),t.attachEvent("onAfterTaskUpdate",t._lightBoxSaveHandler)},t.attachEvent("onGanttReady",function(){t._attachAutoSchedulingHandlers(),t._attachAutoSchedulingHandlers=function(){}}),t.getConnectedGroup=function(n){var r=a.getLinkedTasks();return void 0!==n?t.getTask(n).type==t.config.types.project?{tasks:[],links:[]}:e(n,r,{}):function(t){for(var n,r,a,i={},o=[],s=0;s<t.length;s++)if(n=t[s].source,r=t[s].target,a=null,i[n]?i[r]||(a=r):a=n,a){var u=t.length;o.push(e(a,t,i)),u!=t.length&&(s=-1)}return o}(r)}}()},3:function(t,e){var n={second:1,minute:60,hour:3600,day:86400,week:604800,month:2592e3,quarter:7776e3,year:31536e3};function r(t,e){var n=[];if(t.filter)return t.filter(e);for(var r=0;r<t.length;r++)e(t[r],r)&&(n[n.length]=t[r]);return n}t.exports={getSecondsInUnit:function(t){return n[t]||n.hour},forEach:function(t,e){if(t.forEach)t.forEach(e);else for(var n=t.slice(),r=0;r<n.length;r++)e(n[r],r)},arrayMap:function(t,e){if(t.map)return t.map(e);for(var n=t.slice(),r=[],a=0;a<n.length;a++)r.push(e(n[a],a));return r},arrayFind:function(t,e){if(t.find)return t.find(e);for(var n=0;n<t.length;n++)if(e(t[n],n))return t[n]},arrayFilter:r,arrayDifference:function(t,e){return r(t,function(t,n){return!e(t,n)})},arraySome:function(t,e){if(0===t.length)return!1;for(var n=0;n<t.length;n++)if(e(t[n],n,t))return!0;return!1},hashToArray:function(t){var e=[];for(var n in t)t.hasOwnProperty(n)&&e.push(t[n]);return e},sortArrayOfHash:function(t,e,n){var r=function(t,e){return t<e};t.sort(function(t,a){return t[e]===a[e]?0:n?r(t[e],a[e]):r(a[e],t[e])})},throttle:function(t,e){var n=!1;return function(){n||(t.apply(null,arguments),n=!0,setTimeout(function(){n=!1},e))}},isArray:function(t){return Array.isArray?Array.isArray(t):t&&void 0!==t.length&&t.pop&&t.push},isDate:function(t){return!(!t||"object"!=typeof t||!(t.getFullYear&&t.getMonth&&t.getDate))},isStringObject:function(t){return t&&"object"==typeof t&&"function String() { [native code] }"===Function.prototype.toString.call(t.constructor)},isNumberObject:function(t){return t&&"object"==typeof t&&"function Number() { [native code] }"===Function.prototype.toString.call(t.constructor)},isBooleanObject:function(t){return t&&"object"==typeof t&&"function Boolean() { [native code] }"===Function.prototype.toString.call(t.constructor)},delay:function(t,e){var n;return function(){clearTimeout(n),n=setTimeout(function(){t()},e)}}}},9:function(t,e){t.exports=function(t){return{getVirtualRoot:function(){return t.mixin(t.getSubtaskDates(),{id:t.config.root_id,type:t.config.types.project,$source:[],$target:[],$virtual:!0})},getLinkedTasks:function(e,n){var r=[e],a=!1;t._isLinksCacheEnabled()||(t._startLinksCache(),a=!0);for(var i=[],o={},s={},u=0;u<r.length;u++)this._getLinkedTasks(r[u],o,n,s);for(var u in s)i.push(s[u]);return a&&t._endLinksCache(),i},_collectRelations:function(e,n,r,a){var i,o=t._getSuccessors(e,n),s=[];r&&(s=t._getPredecessors(e,n));for(var u=[],c=0;c<o.length;c++)a[i=o[c].hashSum]||(a[i]=!0,u.push(o[c]));for(c=0;c<s.length;c++)a[i=s[c].hashSum]||(a[i]=!0,u.push(s[c]));return u},_getLinkedTasks:function(e,n,r,a){for(var i,o=void 0===e?t.config.root_id:e,s=(n={},{}),u=[{from:o,includePredecessors:r,isChild:!1}];u.length;){var c=u.pop(),l=c.isChild;if(!n[o=c.from]){i=t.isTaskExists(o)?t.getTask(o):this.getVirtualRoot(),n[o]=!0;for(var d=this._collectRelations(i,l,r,s),g=0;g<d.length;g++){var f=d[g];a[f.hashSum]=f;var h=f.sourceParent==f.targetParent;n[f.target]||u.push({from:f.target,includePredecessors:!0,isChild:h})}if(t.hasChild(i.id)){var _=t.getChildren(i.id);for(g=0;g<_.length;g++)n[_[g]]||u.push({from:_[g],includePredecessors:!0,isChild:!0})}}}return a}}}}})});