"""
Copyright (c) 2011 Hearsay News Products, Inc.
  
Permission is hereby granted, free of charge, to any person obtaining a copy 
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
   
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
   
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
"""

import sys
from optparse import OptionParser

import sleekxmpp

# Make sure Unicode is properly handled
if sys.version_info < (3, 0):
    reload(sys)
    sys.setdefaultencoding('utf8')


"""
Handler for receiving message notifications.  Passes the messages through
a socket to be received by a PHP server, where they can be properly handled
within Symfony.
"""
class Listener(sleekxmpp.ClientXMPP):
    
    def __init__(self, jid, password):
        sleekxmpp.ClientXMPP.__init__(self, jid, password)

        # All we care about is handling message events
        self.add_event_handler('message', self.message)

    def message(self, message):
        print message.body

if __name__ == '__main__':
    optp = OptionParser()
    optp.add_option('-j', '--jid', dest='jid', help='JID to use')
    optp.add_option('-p', '--password', dest='password',
                    help='Password to use')
    optp.add_option('-P', '--port', dest='port',
                    help='Port to connect on')
    optp.add_option('-h', '--host', dest='host',
                    help='Host to connect to')
    
    opts, args = optp.parse_args()

    listener = Listener(opts.jid, opts.password)
    listener.registerPlugin('xep_0060')
    if listener.connect((opts.host, opts.port)):
        print 'Listening for messages.'
        listener.process(threaded = False)
        print 'All done.'
    else:
        print 'Could not connect.'
