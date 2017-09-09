<div ng-app="app">
	<style type="text/css">
		table { border-collapse: collapse; }
.percent {
    position: absolute; width: 300px; height: 14px; z-index: 1; text-align: center; font-size: 0.8em; color: white;
}
.progress-bar {
    width: 300px; height: 14px;
    border-radius: 10px;
    border: 1px solid #CCC;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#6666cc), to(#4b4b95));
    border-image: initial;
}
.uploaded {
    padding: 0;
    height: 14px;
    border-radius: 10px;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#66cc00), to(#4b9500));
    border-image: initial;
}
.dropbox {
    width: 15em;
    height: 3em;
    border: 2px solid #DDD;
    border-radius: 8px;
    background-color: #FEFFEC;
    text-align: center;
    color: #BBB;
    font-size: 2em;
    font-family: Arial, sans-serif;
}
.dropbox span {
    margin-top: 0.9em;
    display: block;
}
.dropbox.not-available {
    background-color: #F88;
}
.dropbox.over {
    background-color: #bfb;
}
.upload{
	margin-left: 30px;
}
.upload1{
	margin-left: 15px;
}
	</style>
	<script type="text/javascript">
		angular.module('app', [], function() {})
		FileUploadCtrl.$inject = ['$scope']
		function FileUploadCtrl(scope) {
		    var dropbox = document.getElementById("dropbox")
		    scope.dropText = 'Drop files here...'
		
		    // init event handlers
		    function dragEnterLeave(evt) {
		        evt.stopPropagation()
		        evt.preventDefault()
		        scope.$apply(function(){
		            scope.dropText = 'Drop files here...'
		            scope.dropClass = ''
		        })
		    }
		    dropbox.addEventListener("dragenter", dragEnterLeave, false)
		    dropbox.addEventListener("dragleave", dragEnterLeave, false)
		    dropbox.addEventListener("dragover", function(evt) {
		        evt.stopPropagation()
		        evt.preventDefault()
		        var clazz = 'not-available'
		        var ok = evt.dataTransfer && evt.dataTransfer.types && evt.dataTransfer.types.indexOf('Files') >= 0
		        scope.$apply(function(){
		            scope.dropText = ok ? 'Drop files here...' : 'Only files are allowed!'
		            scope.dropClass = ok ? 'over' : 'not-available'
		        })
		    }, false)
		    dropbox.addEventListener("drop", function(evt) {
		        console.log('drop evt:', JSON.parse(JSON.stringify(evt.dataTransfer)))
		        evt.stopPropagation()
		        evt.preventDefault()
		        scope.$apply(function(){
		            scope.dropText = 'Drop files here...'
		            scope.dropClass = ''
		        })
		        var files = evt.dataTransfer.files
		        if (files.length > 0) {
		            scope.$apply(function(){
		                scope.files = []
		                for (var i = 0; i < files.length; i++) {
		                    scope.files.push(files[i])
		                }
		            })
		        }
		    }, false)
		    //============== DRAG & DROP =============
		
		    scope.setFiles = function(element) {
		    scope.$apply(function(scope) {
		      console.log('files:', element.files);
		        scope.files = []
		        for (var i = 0; i < element.files.length; i++) {
		          scope.files.push(element.files[i])
		        }
		      scope.progressVisible = false
		      });
		    };
		
		    scope.uploadFile = function() {
		        var fd = new FormData()
		        for (var i in scope.files) {
		            fd.append("uploadedFile", scope.files[i])
		        }
		        var xhr = new XMLHttpRequest()
		        xhr.upload.addEventListener("progress", uploadProgress, false)
		        xhr.addEventListener("load", uploadComplete, false)
		        xhr.addEventListener("error", uploadFailed, false)
		        xhr.addEventListener("abort", uploadCanceled, false)
		        xhr.open("POST", "/ad/upload")
		        scope.progressVisible = true
		        xhr.send(fd)
		    }
		
		    function uploadProgress(evt) {
		        scope.$apply(function(){
		            if (evt.lengthComputable) {
		                scope.progress = Math.round(evt.loaded * 100 / evt.total)
		            } else {
		                scope.progress = 'unable to compute'
		            }
		        })
		    }
		
		    function uploadComplete(evt) {
		        /* This event is raised when the server send back a response */
		        alert(evt.target.responseText)
		    }
		
		    function uploadFailed(evt) {
		        alert("There was an error attempting to upload the file.")
		    }
		
		    function uploadCanceled(evt) {
		        scope.$apply(function(){
		            scope.progressVisible = false
		        })
		        alert("The upload has been canceled by the user or the browser dropped the connection.")
		    }
		}


	</script>
<div ng-controller="FileUploadCtrl">
    <div class="row">
        <label for="fileToUpload" class="upload">上传文件</label><br />
        <div>
            <input class="btn btn-default upload" type="file" ng-model-instant id="fileToUpload" name = "files" multiple onchange="angular.element(this).scope().setFiles(this)" />
        </div>
    </div>
    <div id="dropbox" class="dropbox upload1" ng-class="dropClass"><span>{{dropText}}</span></div>
    <div ng-show="files.length">
        <div ng-repeat="file in files.slice(0)">
            <span>{{file.webkitRelativePath || file.name}}</span>
            (<span ng-switch="file.size > 1024*1024">
                <span ng-switch-when="true">{{file.size / 1024 / 1024 | number:2}} MB</span>
                <span ng-switch-default>{{file.size / 1024 | number:2}} kB</span>
            </span>)
        </div>
        <input type="button" ng-click="uploadFile()" value="Upload" />
        <div ng-show="progressVisible">
            <div class="percent">{{progress}}%</div>
            <div class="progress-bar">
                <div class="uploaded" ng-style="{'width': progress+'%'}"></div>
            </div>
        </div>
    </div>
</div>
</div>