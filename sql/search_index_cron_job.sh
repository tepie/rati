#!/bin/bash 
echo "Galileo Data Visualization Search Index CRON Job"
export VISUALIZ_INSTALL_DIR=
echo "Installation directory is set as.... $VISUALIZ_INSTALL_DIR" 
echo "Executing index replacement...."
php -f $VISUALIZ_INSTALL_DIR/sql/search_index_replace.php
echo "Executing index weights...."
php -f $VISUALIZ_INSTALL_DIR/sql/search_index_weights.php
echo "Search indexing is complete!"
exit