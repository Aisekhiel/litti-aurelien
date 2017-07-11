var kmns_notice = {
  title   : '',
  content : '',
  autohide : true,
  TYPE_NOTICE : {
    success : 'notice--success',
    warning : 'notice--warning',
    danger  : 'notice--danger',
  },
  construct : function() {
    var self              = this;
    this.$body            = $('body');
    this.$notice          = this.$body.find('.kmns-notice');
    this.$notice_title    = this.$notice.find('.notice-title');
    this.$notice_content  = this.$notice.find('.notice-content');

    this.$notice.on('click', '.control-close', function() {
      clearTimeout(this.timeout_hide);
      self.hide();
    }).on('mouseenter', function() {
      clearTimeout(self.timeout_hide);
    }).on('mouseleave', function() {
      clearTimeout(self.timeout_hide);
      self.timeout_hide = setTimeout(function() {
        self.hide();
      }, self.delay_before_hide);
    });

    return this;
  },
  fill : function(title, content, type_notice) {
    this.$notice.addClass(type_notice);
    this.$notice_title.html(title);
    this.$notice_content.html(content);

    return this;
  },
  timeout_unlock      : null,
  delay_before_unlock : 700,
  lock_show           : false,
  timeout_hide        : null,
  delay_before_hide   : 4000,
  show : function() {
    var self = this;
    if(self.lock_show == false) {
      if(self.$notice_title.html() == '' &&
          self.$notice_content.html() == '') {
        console.error('[KMNS-NOTICE] You must fill the notice first !');
      } else {
        self.$notice.addClass('notice--visible');

        // Autohide
        if(self.autohide == true) {
          clearTimeout(self.timeout_hide);
          self.timeout_hide = setTimeout(function() {
            self.hide();
          }, self.delay_before_hide);
        }
      }
    }

    return this;
  },
  hide : function() {
    var self = this;
    self.$notice.removeClass('notice--visible');
    self.lock_show = true;

    clearTimeout(self.timeout_unlock);

    self.timeout_unlock = setTimeout(function() {
      self.lock_show = false;

      // Clean NOTICE TYPE style
      for(var key in self.TYPE_NOTICE) {
        self.$notice.removeClass(self.TYPE_NOTICE[key]);
      }
    }, self.delay_before_unlock);

    return this;
  }
};



var kaiminus = {
  construct : function(contact_url) {
    var self = this;
    self.$body = $('body');

    if(typeof(contact_url) != 'undefined' && contact_url != '')
      self.contact_url = contact_url;

    // Events
    // Event > Sidebar
    self.$sidebar = self.$body.find('.kmns-sidebar');
    self.$sidebar.on('click', '.button-display-about-me', function() {
      self.toggle_about_me();
    });

    // Event > Contact
    self.$body.on('click', '.button-display-contact', function() {
      self.toggle_contact();
    });

    self.$body.on('blur', '.form-control', function() {
      if(this.value != '')
        $(this).addClass('control--filled');
      else
        $(this).removeClass('control--filled');
    }).find('.form-control').each(function() {
      if(this.value != '')
        $(this).addClass('control--filled');
    });

    // Event > Contact Me Form Submit
    self.$contact_forms = self.$body.find('.form-contact-me');
    self.$contact_forms.on('submit', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var $form = $(this);
      var $inputs = $form.find('input, textarea');

      self.prepare_contact_me($inputs);

      self.contact_me(function(response) {
        if(response.errors.length < 1) {
          kmns_notice.autohide = true;
          kmns_notice.fill(
            'Message envoyé !',
            'Votre message a correctement été envoyé, je vous répondrai bientôt.',
            kmns_notice.TYPE_NOTICE.success
          );

          $inputs.each(function() {
            $(this).val('').removeClass('control--filled');
          });
        } else {
          kmns_notice.autohide = false;
          kmns_notice.fill(
            'Un problème est survenu',
            response.errors.join('<br>'),
            kmns_notice.TYPE_NOTICE.danger
          );
        }

        kmns_notice.show();
      });

      return false;
    });
  },

  about_is_visible: false,
  toggle_about_me : function() {
    this.$body.toggleClass('kmns-status--about-me-visible');
    this.about_is_visible = !this.about_is_visible;
  },

  contact_is_visible: false,
  toggle_contact : function() {
    this.$body.toggleClass('kmns-status--contact-visible')
  },

  contact_me_data : {},
  prepare_contact_me : function($inputs) {
    this.contact_me_data = {
      name    : $inputs.filter('[name="contact-name"]').val(),
      email   : $inputs.filter('[name="contact-email"]').val(),
      message : $inputs.filter('[name="contact-message"]').val(),
      phone   : $inputs.filter('[name="contact-phone"]').val(),
      website : $inputs.filter('[name="contact-website"]').val()
    };
  },

  contact_errors  : [],
  contact_url     : null,
  is_sending      : false,
  contact_me : function(response_callback) {
    var self = this;
    // IF not already sending an email THEN ...
    if(self.is_sending == false) {
      self.contact_errors = [];
      self.is_sending = true;

      $.ajax({
        type  : "post",
        url   : self.contact_url,
        data  : self.contact_me_data,
        success : function(response) {
          self.reset_contact_me();

          if(typeof(response_callback) != 'undefined')
            response_callback(response);
        }
      });
    }
  },
  reset_contact_me : function() {
    this.is_sending      = false;
    this.contact_me_data = {};
  }
};
