#!/bin/bash 
echo "Galileo Data Visualization Search Index CRON Job"
echo "Time Check:" `date`
export VISUALIZ_INSTALL_DIR="/opt/visual/application/rati"
echo "Installation directory is set as.... $VISUALIZ_INSTALL_DIR" 
echo "Executing index replacement...."
cd $VISUALIZ_INSTALL_DIR/sql
php -f $VISUALIZ_INSTALL_DIR/sql/search_index_replace.php
echo "Time Check:" `date`
echo "Executing index weights...."
php -f $VISUALIZ_INSTALL_DIR/sql/search_index_weights.php
echo "Search indexing is complete!"
echo "Time Check:" `date`
