(function($) {
  var app = {
    tweets: [],
    twitterOptions: {
      params: {
        q: '#VOA', //current show hashtag (default: #VOA)
        include_entities: 1, //include entities
        count: 30, //results per page
        result_type: 'recent'
      }
    },
    ready: false,

    setMessage: function(type, text, iconClass){
      //defaults
      var alertType = (typeof type === 'undefined') ? "alert-info" : type;
      iconClass = (typeof type === 'undefined') ? "icon-headphones" : iconClass;
      //TODO switch different alert types
      $(".alert .loading").hide();
      $(".alert .msg").html("<i class=\"" + iconClass + "\"></i>" + text + "");
    },

    getLiveData: function(){
        //get show info from Airtime API
          $.ajax({
                  url: app.LIVE_DATA_URL,
                  dataType: 'jsonp',
                  //crossDomain: true,
                  async: true,
                  success: function(data) {
                    console.log(data);
                    if(data.currentShow[0] && typeof data.currentShow[0].name !== undefined){
                      $(".jp-info .current-show-name").text(data.currentShow[0].name).show();
                      $(".jp-info .current-show-label").show();
                  }
                    if(data.nextShow[0] && typeof data.nextShow[0].name !== undefined){
                      $(".jp-info .next-show-name").text(data.nextShow[0].name).show();
                      $(".jp-info .next-show-label").show();
                    }
                  },
                  error: function() {
                    console.log("Unable to retreive Live Data.");
                    $(".jp-info .lable").hide();
                    $(".jp-info .current-show-name, .jp-info .next-show-name").text();
                  }
          });
    },

    //TODO add param tweets based on the #hashtag in show's live data
    getTweets: function( options ) {
        querystring = $.param(options.params, true);
        console.log('twitter params: ' + querystring);
        console.log(window.location.origin);
        $.ajax({
          url: window.location.origin + '/gettweets?' + querystring,
          dataType: 'json',
          async: false,
          success: function(data) {
            console.log('twitter ajax success!');
            console.log(data);
            app.outputTweets(data);
          },
          error: function(xhr) {
            console.log('Error: unable to get twitter data: ');
            console.log(xhr.statusText);
          }
        });    
    },

    // Extract relevant data from tweets
    outputTweets: function(data) {
      var tweetStream = $("<ol></ol>").addClass("stream-items");
      $.each( data.statuses, function( index, value ) {
        //save to tweets array
        app.tweets.push({
          id: index,
          time: value.created_at,
          text: value.text,
          user: '@' + value.user.screen_name,
          profile_image_url: value.user.profile_image_url
        });
        var tweetMarkup = '<li class="stream-item stream-item-' + value.user.id + '"><div class="tweet"><div class="content">' +
                           '<a class="account-group stream-item-header" href="http://twitter.com/#!' + value.user.screen_name + '" data-user-id="">' +
                           '<img class="avatar img-rounded" src="' + value.user.profile_image_url  + '" alt="' + value.user.name + '">' +
                           '<strong class="fullname">' + value.user.name + '</strong>' +
                           '<span class="username"><s>@</s><b>' + value.user.screen_name + '</b></span>' +
                           '</a>' +
                           '<p class="js-tweet-text">' + app.addEntitiesToTweetText(value) + '</p>' +
                           '<div class="stream-item-footer"></div>'+
                           '</div><div></li>';
              
        tweetStream.append(tweetMarkup);
        
      });
      //add the tweetStream to page
      $('#twitter-container').html(tweetStream);
      console.log(app.tweets);

    },

    escapeHTML: function(text) {
        return $('<div/>').text(text).html();
    },
     
    addEntitiesToTweetText: function(tweet) {
        //replace entities in tweet text with urls and hashtags, etc.
        if (!(tweet.entities)) {
            return app.escapeHTML(tweet.text);
        }
        var index_map = {};
        $.each(tweet.entities.urls, function(i,entry) {
            index_map[entry.indices[0]] = [entry.indices[1], function(text) {
              return "<a href='" + app.escapeHTML(entry.url) + "'>" + app.escapeHTML(text) + "</a>";
            }];
        });
        $.each(tweet.entities.hashtags, function(i,entry) {
            index_map[entry.indices[0]] = [entry.indices[1], function(text) {
              return "<a href='http://twitter.com/search?q=" + escape("#" + entry.text) + "'>" + app.escapeHTML(text) + "</a>";
            }];
        });
        $.each(tweet.entities.user_mentions, function(i,entry) {
            index_map[entry.indices[0]] = [entry.indices[1], function(text) {
              return "<a title='" + app.escapeHTML(entry.name) + "' href='http://twitter.com/" + app.escapeHTML(entry.screen_name) + "'>" + app.escapeHTML(text) + "</a>";
            }];
        });
        var result = "";
        var last_i = 0;
        var i = 0;
        // iterate through the string looking for matches in the index_map
        for (i=0; i < tweet.text.length; ++i) {
            var ind = index_map[i];
            if (ind) {
                var end = ind[0];
                var func = ind[1];
                if (i > last_i) {
                    result += app.escapeHTML(tweet.text.substring(last_i, i));
                }
                result += func(tweet.text.substring(i, end));
                i = end - 1;
                last_i = end;
            }
        }
        if (i > last_i) {
            result += app.escapeHTML(tweet.text.substring(last_i, i));
        }
        return result;
    }

  }; //app

  window.app = app;

})(jQuery);
