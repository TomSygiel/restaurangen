<?php

class Booking {
	private $connection;
	private $table_name = 'bookings';

	public function __construct($db) {
		$this->connection = $db;
	}

	public function updateBooking($booking_row) {

		$sql = "UPDATE bookings SET costumer_ID = :costumer_ID, guests = :guests, sitting = :sitting WHERE booking_ID = :booking_ID";
		$statement = $this->pdo->prepare($sql);

		$booking_row->$costumer_ID = htmlspecialchars(strip_tags($booking_row->$costumer_ID));
		$booking_row->$guests = htmlspecialchars(strip_tags($booking_row->$guests));
		$booking_row->$sitting = htmlspecialchars(strip_tags($booking_row->$sitting));

		$statement->bindParam(":costumer_ID", $booking_row->costumer_ID);
		$statement->bindParam(":guests", $booking_row->guests);
		$statement->bindParam(":sitting", $booking_row->sitting);
				

		if($statement->execute()) {
			return true;
		}

        return false;
	}
}

class BookingRow {
	public $booking_ID;
	public $costumer_ID;
	public $guests;
	public $sitting;
}

?>