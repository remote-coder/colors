var ColorApp = ColorApp || {};

/*
 * Here's where the front-end color app stuff really happens
 */
ColorApp.content = function() 
{
	//gotta have that for the ajax callback stuff dude
	var that = this;
	
	/*
	 * getColorsSuccess handles the data return for the getColors() ajax call
	 */
	var getColorsSuccess = function(data) {
		//grab the rows and jam em in the object
		$.each(data.data, function(index, row) {
			ColorApp.content.colors[row['color']]= row['color_id'];
		});
		//then render the rid
		$(document).trigger({type:'renderGrid', returnData:data.data });
	};

	/*
	 * getVoteDataSuccess handles the data return for the getVoteData() ajax call
	 */
	var getVoteDataSuccess = function(data) {
		//break it down by color
		$.each(data.data, function(index, row) {
			if(undefined==ColorApp.content.votes[row['color']]) {
				ColorApp.content.votes[row['color']] = {};
			}	
			//break it down by city
			ColorApp.content.votes[row['color']][row['city']]= {votes:row['votes']};
		});
		//then rock the vote
		$(document).trigger({type:'populateVotes', returnData:data });
	}; 

    return {
    	colors: Object(),
    	votes: Object(),
    	isTotalDisplayed: false,
    	
    	/*
    	 * getTemplate - go fetch the html template I want
    	 */
    	getTemplate: function(templateName)
        {
            data = $("#"+templateName).html();
        	return data;
        },

        /**
         * getColors - ajax call to go fetch some colors
         */
        getColors: function()
        {
            $.ajax({
                type: "POST",
                url: '/stash/GETSOME/',
                dataType : 'json',
                data: {type:'colors', 'postToken':123456},
                success: function(data) {getColorsSuccess.call(that, data);},
                error: ColorApp.content.handleError
            });
        },
        
        /**
         * getVoteData - pretty much what it sounds like
         * the data package will have the color in it, so use that as an arg
         */
        getVoteData: function(data)
        {
            $.ajax({
                type: "POST",
                url: '/stash/GETSOME/',
                dataType : 'json',
                data: { type: 'votes', color: data.color},
                success: function(data) {getVoteDataSuccess.call(that, data);},
                error: ColorApp.content.handleError
            });
        },
        
        /**
         * if ya got an error and have a modern browser, feel free to read your js console
         * otherwise,I'm sorry to hear that you don't have a modern browser
         */
        handleError: function(data) {
        	if (undefined==console || undefined==console.log) {
        		return;
        	}
        	console.log('=======error=======');
        	console.log(data);
        },
        
        /**
         * Render the grid!
         */
        renderGrid: function(data) {
        	//fetch the gridRow template that its all based on
        	var newRow = ColorApp.content.getTemplate('gridRow');

        	//then do this for every data row your have
        	$.each(data, function(index,colorData) {

        		//figure out if it is even or odd based on the index
            	var even = index%2 == 0 ? true : false;

            	//scoped copy of the gridrow template
            	var row = newRow;

            	//get a list of substitution tags like ${thisIsATag}
            	//normally I'd use an real js templater, but keepin it simple
            	var tags = row.match(/\${[^\${\r\n]*}/g);
            	
            	//If you found tag matches, replace em with their 
            	//corresponding data element
            	$.each(tags, function(a, tag) {
            		namedItem = tag.replace(/[${}]/g,'');
            		row = row.replace(tag, colorData[namedItem]);
            	});

            	var output = $(row);
            	if(even) {
            		output.addClass('oddRow');
            	} else {
            		output.addClass('evenRow');
            	}

            	//make it data here for easier use later
    			output.data(data[index]);
    			
    			//now stick it in the table
            	$("#colorTable").append(output);

        	}); 
        	
        	//whats that you say? 
        	//you want to bind the click to colors and have that fetch data?
        	//and you want a little glowy when someone mouses over a clickable color?
    		$(".colorTable tr td:first-child").click(
        			function() {
        				var data = $(this).parent().data() ;
        				ColorApp.content.getVoteData(data);
        			}
    		)
    		.hover(
				function(){ ColorApp.utils.glow(this);},
				function(){ $(this).css({ opacity: 1 });}
    		);

    		
        	var lastRow = $("#colorTable tr:last-child");
        	newRow = $(ColorApp.content.getTemplate('totalRow'));
        	classToggle = lastRow[0]['className']=='evenRow'? 'oddRow' : 'evenRow';
        	newRow.addClass(classToggle);
        	$("#colorTable").append(newRow);
        	
        	//Don't forget about the Total line!
        	$("#colorTable tr:last-child td:first-child").click( function(){
        		ColorApp.content.renderTotal();
        	})
        	.hover(
				function(){ ColorApp.utils.glow(this);},
				function(){ $(this).css({ opacity: 1 });}
    		);

        },
        
        /*
         * Populate the vote for whatever data is sent here
         */
        populateVotes: function(data) {
        	//get the target id for the output
    		var id = '#value_'+ColorApp.content.colors[data.qualified.color];
        	
        	var total = 0;
        	var textValue = "";
        	//for each data row format the data, make it a li and jam it in the ul
        	$.each(data.data, function(index,voteData) {
        		total += parseInt( voteData.votes );
        		var votes = ColorApp.utils.numberFormat(voteData.votes);
        		textValue += "<li><span>"+voteData.city+ ":</span><span class='cityTotal'>"+ votes+"</span></li>";
    		}); 

        	if(textValue.length==0) {
        		textValue = "<li><span>&nbsp;</span><span class='cityTotal'> [No Votes] </span></li>";
        	} else {
        		textValue += "<li><span>&nbsp;</span><span class='cityTotal'>-----------</span></li>";
        		textValue += "<li><span>&nbsp;</span><span class='cityTotal'>"+ ColorApp.utils.numberFormat(total)+"</span></li>";
        	}
        	
        	$(id).html("<ul>"+textValue+"<ul>");
        	
        	//if you are already showing the total, recalc it and display
        	if(ColorApp.content.isTotalDisplayed===true) {
        		ColorApp.content.renderTotal();
        	}
        },
        
        /**
         * so...ya.  calculate the total and put it where it belongs
         */
        renderTotal: function() {
        	var votes = ColorApp.utils.totalVotes(ColorApp.content.votes);
        	$("#totalValue").html("<ul><span>&nbsp;</span><span class='cityTotal'>"+ColorApp.utils.numberFormat(votes)+"</span><ul>");
        	ColorApp.content.isTotalDisplayed = true;
        }

    };
}();


/**
 * some utils
 */
ColorApp.utils = (function() 
{
	return {
		    	
		centerOnPage: function(id)
		{
			var width = parseInt($("#"+id).width())  ;
			var left = parseInt(document.body.offsetWidth) - width;
		    $("#"+id).css({'left': left/2, 'position': 'relative'});
		},
		        
		totalVotes: function(workObject, key)
		{ 
			if(undefined==key) {
				key='all';
			}
		    var filterKey = key; 
		    var total = 0;
		    $.each(workObject, function(index,colorGroup) {
		    	if(filterKey=='all' || filterKey ==index){
		        	$.each(colorGroup, function(index,cityRow) {
			        	total += parseInt(cityRow.votes);
		        	});
		       	}
		   	});
		    return total;
		},
		        
		numberFormat: function(value)
		{
			return Object(value).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		},
		
		glow: function (jqObject,stop) 
		{
			if(undefined==stop)
			{
				$(jqObject)
				.animate({ opacity: 0.2, },50, 'linear')
				.animate({ opacity: 1 }, 50, 'linear', ColorApp.utils.glow);
						
			}
			
				
			
		}
		
	};
}());

/* set the init function.  
 * This stuff gets run after all the essentials loaded and the page is ready
 * It sets the binds, pops the main page, fetches the colors data, centers the 
 * color table, and sets the page resize action 
 */
ColorApp.init = (
	function()
	{
		$(document).bind('renderGrid',  function(scooby){ColorApp.content.renderGrid(scooby.returnData);});
		$(document).bind('populateVotes',  function(scooby){ColorApp.content.populateVotes(scooby.returnData);});
    	$("#main-content-container").append(ColorApp.content.getTemplate('mainPage')); 
		ColorApp.content.getColors(); 
		ColorApp.utils.centerOnPage('colorTable');
		$(window).resize(function(){ColorApp.utils.centerOnPage('colorTable');});
	}
);
