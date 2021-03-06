(function() {
	'use strict'

	angular.module('roomSetView')
		.controller('roomSetListCtrl', function($scope, $state, $stateParams, roomSetData, residenceData, ajax, conferenceInfo, roomTypes, permissions, confirmDeleteModal) {
			var residence = {}

			for (var i = residenceData[0].length - 1; i >= 0; i--) {
				if (residenceData[0][i].id == $stateParams.rid) {
					residence = residenceData[0][i]
					continue
				}
			};

			$scope.canEdit = (permissions.indexOf('conference-room-edit') !== -1)
			
			var cid = $stateParams.cid
			$scope.roomSets = roomSetData[0] || []
			$scope.noRoomSets = ($scope.roomSets.length === 0)
			$scope.conferenceName = conferenceInfo.data.name
			$scope.residence = residence

			$scope.showWidget = function(toState, params) {
				var state = 'dashboard.conferences.manage.' + toState;
				var allParams = {cid: $stateParams.cid}
				if (params) {
					angular.forEach(params, function(val, key) {
						allParams[key] = val
					})
				}
				$state.go(state, allParams);
			}

			$scope.deleteRoomSet = function(roomSet) {
				confirmDeleteModal.open('Room Set', roomSet.name, 'api/conferences/' + $stateParams.cid + '/residences/roomSets/' + roomSet.id)
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.goToResidence = function () {
				$state.go('dashboard.conferences.manage.viewResidence', {cid: $stateParams.cid})
			}

		})

})()